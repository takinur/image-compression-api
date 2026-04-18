import type { CompressionOptions, FilePondFile, ApiUploadResponse } from './types';

// FilePond and browser-image-compression are loaded from CDN in upload.html
declare const FilePond: {
  create: (element: HTMLInputElement, options: Partial<FilePondOptions>) => FilePondInstance;
};

declare function imageCompression(file: File, options: CompressionOptions): Promise<Blob>;

interface FilePondOptions {
  instantUpload: boolean;
  allowProcess: boolean;
  storeAsFile: boolean;
  allowMultiple: boolean;
  acceptedFileTypes: string[];
  allowDrop: boolean;
  labelIdle: string;
}

interface FilePondInstance {
  getFiles(): FilePondFile[];
  removeFiles(): void;
  onaddfile: (() => void) | null;
}

const MIN_IMAGES = 18;
const MAX_IMAGES = 25;
const MAX_TOTAL_SIZE_BYTES = 150 * 1024 * 1024;

// DOM references
const input = document.querySelector<HTMLInputElement>('input[type="file"]')!;
const form = document.getElementById('form') as HTMLFormElement;
const loader = document.getElementById('loader') as HTMLDivElement;
const successDiv = document.getElementById('success') as HTMLDivElement;
const button = document.getElementById('button') as HTMLButtonElement;
const countLabel = document.getElementById('fileCount') as HTMLHeadingElement;

// Initialise FilePond
const pond: FilePondInstance = FilePond.create(input, {
  instantUpload: false,
  allowProcess: false,
  storeAsFile: true,
  allowMultiple: true,
  acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
  allowDrop: false,
  labelIdle: '<span class="filepond--label-action">Browse</span> images',
});

pond.onaddfile = (): void => {
  countLabel.textContent = `Total Selected Images: ${pond.getFiles().length}`;
};

document
  .querySelector<HTMLElement>('.filepond--drop-label')
  ?.addEventListener('click', () => pond.removeFiles());

async function compressFile(pondFile: FilePondFile): Promise<File> {
  const { file } = pondFile;

  const options: CompressionOptions = {
    maxSizeMB: 1,
    maxWidthOrHeight: 512,
    useWebWorker: true,
    fileType: 'image/png',
  };

  const compressed = await imageCompression(file, options);
  const outputName = file.name.replace(/\.(png|jpeg)$/i, '.jpg');

  return new File([compressed], outputName, {
    type: compressed.type,
    lastModified: Date.now(),
  });
}

async function uploadFiles(pondFiles: FilePondFile[]): Promise<void> {
  const compressedFiles: File[] = [];

  for (const pondFile of pondFiles) {
    compressedFiles.push(await compressFile(pondFile));
  }

  const formData = new FormData();
  compressedFiles.forEach((file) => formData.append('files', file));

  const className = (document.getElementById('className') as HTMLSelectElement).value;
  const sessionId = new URLSearchParams(window.location.search).get('session_id');

  const response = await fetch(`/api/upload?id=${sessionId}&class=${className}`, {
    method: 'POST',
    body: formData,
  });

  if (!response.ok) {
    const data: ApiUploadResponse = await response.json().catch(() => ({}));
    throw new Error(data.error ?? `Upload failed (${response.status})`);
  }

  loader.style.display = 'none';
  successDiv.style.display = 'flex';
}

form.addEventListener('submit', async (e: Event): Promise<void> => {
  e.preventDefault();

  const pondFiles = pond.getFiles();

  if (pondFiles.length < MIN_IMAGES) {
    alert(`Please upload at least ${MIN_IMAGES} images.`);
    return;
  }

  if (pondFiles.length > MAX_IMAGES) {
    alert(`Maximum ${MAX_IMAGES} images allowed.`);
    return;
  }

  const totalSize = pondFiles.reduce((sum, f) => sum + f.file.size, 0);
  if (totalSize > MAX_TOTAL_SIZE_BYTES) {
    alert('Total image size must be under 150MB.');
    return;
  }

  button.disabled = true;
  form.style.display = 'none';
  loader.style.display = 'block';

  try {
    await uploadFiles(pondFiles);
  } catch (err) {
    console.error('Upload error:', err);
    alert('Something went wrong — please try again.');
    button.disabled = false;
    form.style.display = 'block';
    loader.style.display = 'none';
  }
});
