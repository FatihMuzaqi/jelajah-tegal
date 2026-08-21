<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CatalogEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VirtualTourController extends Controller
{
    /**
     * Serve static files for the virtual tour.
     */
    public function serve(Request $request, string $domain, string $slug, string $path = 'index.html')
    {
        // Find the entity
        $entity = CatalogEntity::where('slug', $slug)
            ->whereHas('serviceType', function ($q) use ($domain) {
                $q->where('code', $domain);
            })
            ->where('has_virtual_tour', true)
            ->firstOrFail();

        // Security: Prevent directory traversal
        if (str_contains($path, '../') || str_contains($path, '..\\')) {
            abort(404);
        }

        $basePath = Storage::disk('public')->path('virtual-tours/' . $entity->id);
        $fullPath = $basePath . '/' . $path;

        if (!File::exists($fullPath)) {
            // Some requests might try to access directory without index.html
            if (File::isDirectory($fullPath)) {
                $fullPath = rtrim($fullPath, '/') . '/index.html';
                if (!File::exists($fullPath)) {
                    abort(404);
                }
            } else {
                abort(404);
            }
        }

        // Determine mime type
        $mimeType = $this->getMimeType($fullPath);
        
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=3600'
        ]);
    }

    private function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $mimeTypes = [
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'webp' => 'image/webp',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mp4' => 'video/mp4',
            'mkv' => 'video/x-matroska',
            
            // fonts
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject',
            'otf' => 'font/otf',
        ];

        return $mimeTypes[$extension] ?? File::mimeType($path);
    }
}
