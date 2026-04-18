/**
 * Utility functions for client-side image resizing using the Canvas API.
 * Used as an alternative to browser-image-compression for direct canvas control.
 */

export interface ResizeDimensions {
  width: number;
  height: number;
}

const DEFAULT_JPEG_QUALITY = 0.7;

/**
 * Calculates new dimensions while preserving the original aspect ratio.
 */
export function calculateSize(
  img: HTMLImageElement,
  maxWidth: number,
  maxHeight: number
): ResizeDimensions {
  let { width, height } = img;

  if (width > height) {
    if (width > maxWidth) {
      height = Math.round((height * maxWidth) / width);
      width = maxWidth;
    }
  } else {
    if (height > maxHeight) {
      width = Math.round((width * maxHeight) / height);
      height = maxHeight;
    }
  }

  return { width, height };
}

/**
 * Resizes an image file using the Canvas API, preserving aspect ratio.
 * Returns a new compressed File at the specified max dimensions and quality.
 */
export function resizeImage(
  file: File,
  maxWidth: number = 512,
  maxHeight: number = 512,
  quality: number = DEFAULT_JPEG_QUALITY
): Promise<File> {
  return new Promise((resolve, reject) => {
    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = () => {
      URL.revokeObjectURL(objectUrl);

      const { width, height } = calculateSize(img, maxWidth, maxHeight);

      const canvas = document.createElement('canvas');
      canvas.width = width;
      canvas.height = height;

      const ctx = canvas.getContext('2d');
      if (!ctx) {
        reject(new Error('Canvas 2D context unavailable'));
        return;
      }

      ctx.drawImage(img, 0, 0, width, height);

      canvas.toBlob(
        (blob) => {
          if (!blob) {
            reject(new Error('Failed to convert canvas to Blob'));
            return;
          }
          const outputName = file.name.replace(/\.(jpg|jpeg|png)$/i, '.jpg');
          resolve(
            new File([blob], outputName, {
              type: 'image/jpeg',
              lastModified: Date.now(),
            })
          );
        },
        'image/jpeg',
        quality
      );
    };

    img.onerror = () => reject(new Error(`Failed to load image: ${file.name}`));
    img.src = objectUrl;
  });
}
