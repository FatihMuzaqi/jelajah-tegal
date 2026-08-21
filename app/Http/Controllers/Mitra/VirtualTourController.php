<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\CatalogEntity;
use App\Services\VirtualTourProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class VirtualTourController extends Controller
{
    use \App\Http\Controllers\Mitra\Concerns\ResolvesActiveMitra;

    public function upload(Request $request, string $domain, CatalogEntity $entity, VirtualTourProcessor $processor): JsonResponse
    {
        // Ensure user can manage this entity
        if ($request->user()->cannot('tourism.manage') && $request->user()->cannot('accommodation.manage')) {
            abort(403);
        }
        
        if ($entity->mitra_id !== $this->activeMitra($request)->id) {
            abort(403);
        }

        $resumableIdentifier = $request->input('resumableIdentifier');
        $resumableFilename = $request->input('resumableFilename');
        $resumableChunkNumber = (int) $request->input('resumableChunkNumber');
        $resumableTotalChunks = (int) $request->input('resumableTotalChunks');

        if (!$resumableIdentifier || !$request->hasFile('file')) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $tempDir = storage_path('app/temp/virtual-tours/' . $resumableIdentifier);
        if (!File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $chunkFile = $tempDir . '/' . $resumableChunkNumber . '.part';
        $request->file('file')->move($tempDir, $resumableChunkNumber . '.part');

        // Check if all chunks are uploaded
        $uploadedChunks = 0;
        for ($i = 1; $i <= $resumableTotalChunks; $i++) {
            if (File::exists($tempDir . '/' . $i . '.part')) {
                $uploadedChunks++;
            }
        }

        if ($uploadedChunks === $resumableTotalChunks) {
            // Reassemble
            $finalFilePath = $tempDir . '/' . $resumableFilename;
            $finalFile = fopen($finalFilePath, 'w');
            
            for ($i = 1; $i <= $resumableTotalChunks; $i++) {
                $partPath = $tempDir . '/' . $i . '.part';
                $partFile = fopen($partPath, 'r');
                stream_copy_to_stream($partFile, $finalFile);
                fclose($partFile);
                unlink($partPath); // delete part
            }
            fclose($finalFile);

            try {
                // Process zip
                $processor->process($finalFilePath, $entity->id, $domain, $entity->slug);
                
                // Update database
                $entity->update(['has_virtual_tour' => true]);
                
                // Cleanup zip and temp dir
                unlink($finalFilePath);
                rmdir($tempDir);
                
                return response()->json(['message' => 'Upload complete', 'status' => 'success']);
            } catch (\Exception $e) {
                // Cleanup on failure
                if (File::exists($finalFilePath)) unlink($finalFilePath);
                if (File::exists($tempDir)) File::deleteDirectory($tempDir);
                
                return response()->json(['error' => 'Failed to process virtual tour: ' . $e->getMessage()], 500);
            }
        }

        return response()->json(['message' => 'Chunk uploaded', 'status' => 'uploading']);
    }

    public function destroy(Request $request, string $domain, CatalogEntity $entity): JsonResponse
    {
        // Ensure user can manage this entity
        if ($request->user()->cannot('tourism.manage') && $request->user()->cannot('accommodation.manage')) {
            abort(403);
        }
        
        if ($entity->mitra_id !== $this->activeMitra($request)->id) {
            abort(403);
        }

        DB::transaction(function () use ($entity) {
            $entity->update(['has_virtual_tour' => false]);
            $extractPath = Storage::disk('public')->path('virtual-tours/' . $entity->id);
            if (File::exists($extractPath)) {
                // Use system rm to avoid PHP memory exhaustion (std::bad_alloc)
                // in FrankenPHP when deleting virtual tours with thousands of files.
                exec('rm -rf ' . escapeshellarg($extractPath));
            }
        });

        return response()->json(['message' => 'Virtual tour deleted']);
    }
}
