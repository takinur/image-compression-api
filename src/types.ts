export interface UploadSession {
  sessionId: string;
  customerEmail: string;
}

export type UploadClass = 'man' | 'woman';

export interface CompressionOptions {
  maxSizeMB: number;
  maxWidthOrHeight: number;
  useWebWorker: boolean;
  fileType: string;
}

export interface FilePondFile {
  file: File;
  filename: string;
  fileSize: number;
  fileType: string;
}

export interface ApiUploadResponse {
  success: boolean;
  message?: string;
  error?: string;
}
