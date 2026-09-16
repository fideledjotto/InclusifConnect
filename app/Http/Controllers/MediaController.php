<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MediaController extends Controller
{
    private const ALLOWED_DIRECTORIES = [
        'pdf' => 'resources/pdf',
        'audio' => 'resources/audio',
        'video' => 'resources/video',
        'documents' => 'resources/documents',
    ];

    public function serve(string $type, string $filename)
    {
        $path = $this->resolvePath($type, $filename);

        return $this->buildFileResponse($path, false);
    }

    public function download(string $type, string $filename)
    {
        $path = $this->resolvePath($type, $filename);

        return $this->buildFileResponse($path, true);
    }

    private function resolvePath(string $type, string $filename): string
    {
        $baseDirectory = self::ALLOWED_DIRECTORIES[$type] ?? null;

        if (! $baseDirectory) {
            abort(404, 'Type de média non pris en charge.');
        }

        $safeName = basename($filename);

        if ($safeName === '' || $safeName !== $filename) {
            abort(404, 'Nom de fichier invalide.');
        }

        $path = base_path($baseDirectory.DIRECTORY_SEPARATOR.$safeName);

        if (! is_file($path)) {
            abort(404, 'Fichier introuvable.');
        }

        return $path;
    }

    private function buildFileResponse(string $path, bool $download): BinaryFileResponse
    {
        $fileName = basename($path);
        $mimeType = mime_content_type($path) ?: $this->fallbackMimeType($path);
        $disposition = $download ? 'attachment' : 'inline';

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition.'; filename="'.$fileName.'"',
            'Content-Length' => filesize($path),
            'Cache-Control' => 'public, max-age=3600, immutable',
        ]);
    }

    private function fallbackMimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'pdf' => 'application/pdf',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'txt' => 'text/plain; charset=UTF-8',
            default => 'application/octet-stream',
        };
    }
}
