<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizer
{
    /**
     * Optimize and store an uploaded image to the public disk.
     * Resizes if larger than max width, strips metadata, and compresses.
     *
     * @param  string  $folder  Directory inside public disk (e.g. 'products', 'blog', 'hero_slides')
     * @param  int  $maxWidth  Maximum allowable width in pixels
     * @param  int  $quality  JPEG/WebP compression quality (1-100)
     * @return string Stored file relative path on public disk
     */
    public static function optimizeAndStore(UploadedFile $file, string $folder = 'products', int $maxWidth = 1600, int $quality = 85): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = Str::random(40).'.'.($extension === 'png' ? 'png' : ($extension === 'webp' ? 'webp' : 'jpg'));
        $relativeFolder = trim($folder, '/');
        $storagePath = Storage::disk('public')->path($relativeFolder);

        if (! file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $targetFullPath = $storagePath.DIRECTORY_SEPARATOR.$filename;

        // If GD extension is available, optimize and resize
        if (extension_loaded('gd') && function_exists('imagecreatefromstring')) {
            try {
                $rawContent = file_get_contents($file->getRealPath());
                $srcImage = @imagecreatefromstring($rawContent);

                if ($srcImage !== false) {
                    $width = imagesx($srcImage);
                    $height = imagesy($srcImage);

                    if ($width > $maxWidth) {
                        $newWidth = $maxWidth;
                        $newHeight = (int) round(($height / $width) * $maxWidth);

                        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

                        // Preserve transparency for PNG/WebP
                        if ($extension === 'png' || $extension === 'webp') {
                            imagealphablending($dstImage, false);
                            imagesavealpha($dstImage, true);
                        }

                        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagedestroy($srcImage);
                        $srcImage = $dstImage;
                    }

                    // Save according to format
                    if ($extension === 'png') {
                        imagepng($srcImage, $targetFullPath, 8);
                    } elseif ($extension === 'webp' && function_exists('imagewebp')) {
                        imagewebp($srcImage, $targetFullPath, $quality);
                    } else {
                        imagejpeg($srcImage, $targetFullPath, $quality);
                    }

                    imagedestroy($srcImage);

                    return $relativeFolder.'/'.$filename;
                }
            } catch (\Throwable $e) {
                // Fallback to standard Laravel store if GD error occurs
            }
        }

        // Default storage fallback
        return $file->store($relativeFolder, 'public');
    }
}
