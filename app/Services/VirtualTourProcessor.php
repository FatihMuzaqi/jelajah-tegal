<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Exception;

class VirtualTourProcessor
{
    /**
     * Extract zip file and modify absolute paths to be relative to the virtual tour route.
     *
     * @param string $zipFilePath Absolute path to the zip file.
     * @param string $entityId The ID of the catalog entity.
     * @param string $domain The domain/service type code (e.g., 'wisata').
     * @param string $slug The slug of the catalog entity.
     * @return bool True on success, throws exception on failure.
     */
    public function process(string $zipFilePath, string $entityId, string $domain, string $slug): bool
    {
        $extractPath = Storage::disk('public')->path('virtual-tours/' . $entityId);

        // Ensure directory exists and is empty
        if (File::exists($extractPath)) {
            File::deleteDirectory($extractPath);
        }
        File::makeDirectory($extractPath, 0755, true);

        $zip = new ZipArchive;
        if ($zip->open($zipFilePath) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            throw new Exception("Failed to open zip file.");
        }

        // The prefix for the public URL
        $urlPrefix = "/{$domain}/{$slug}/virtual-tour/";

        $entity = \App\Models\CatalogEntity::find($entityId);
        $tourTitle = $entity ? htmlspecialchars($entity->name) . ' - Virtual Tour | Jelajah Tegal' : 'Virtual Tour | Jelajah Tegal';

        // Use Symfony Finder as an iterator to prevent memory exhaustion (std::bad_alloc)
        // when dealing with thousands of virtual tour files in FrankenPHP.
        $finder = new \Symfony\Component\Finder\Finder();
        $finder->files()->in($extractPath);

        foreach ($finder as $file) {
            $extension = strtolower($file->getExtension());
            
            if (in_array($extension, ['html', 'js', 'xml', 'css'])) {
                $content = File::get($file->getPathname());
                $originalContent = $content;

                // Replace absolute paths starting with "/" (that might refer to root) with the urlPrefix
                // We'll target common attributes: src="/...", href="/...", url('/...')
                $content = preg_replace('/(src|href)=([\'"])\/([^\'"]+)([\'"])/i', '$1=$2' . rtrim($urlPrefix, '/') . '/$3$4', $content);
                $content = preg_replace('/url\(([\'"]?)\/([^\'"\)]+)([\'"]?)\)/i', 'url($1' . rtrim($urlPrefix, '/') . '/$2$3)', $content);

                // For HTML files, inject base href if not present to ensure relative paths work regardless of trailing slash
                if ($extension === 'html') {
                    // Inject title
                    if (str_contains(strtolower($content), '<title></title>')) {
                        $content = str_ireplace('<title></title>', '<title>' . $tourTitle . '</title>', $content);
                    } elseif (!str_contains(strtolower($content), '<title>')) {
                        $content = preg_replace('/<head>/i', '<head><title>' . $tourTitle . '</title>', $content, 1);
                    }

                    // Inject favicon
                    if (!str_contains(strtolower($content), 'rel="icon"')) {
                        $content = preg_replace('/<head>/i', '<head><link rel="icon" type="image/png" href="/images/logo.png">', $content, 1);
                    }

                    if (!str_contains(strtolower($content), '<base href=')) {
                        $content = preg_replace('/<head>/i', '<head><base href="' . $urlPrefix . '">', $content, 1);
                    }
                }

                if ($content !== $originalContent) {
                    File::put($file->getPathname(), $content);
                }
            }
        }

        return true;
    }
}
