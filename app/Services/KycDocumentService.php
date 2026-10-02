<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stocke les documents KYC sur le disque privé "local" (storage/app/kyc/...),
 * jamais accessible directement via une URL publique. Le MIME réel du fichier
 * (et non la seule extension) est vérifié avant écriture.
 */
class KycDocumentService
{
    private const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'application/pdf',
    ];

    public function store(UploadedFile $file, int $userId, string $kind): string
    {
        $realMime = $file->getMimeType();
        if (! in_array($realMime, self::ALLOWED_MIMES, true)) {
            throw new \InvalidArgumentException('Type de fichier non autorisé.');
        }

        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $filename  = $kind . '_' . Str::random(32) . '.' . $extension;
        $path      = "kyc/{$userId}/{$filename}";

        Storage::disk('local')->putFileAs("kyc/{$userId}", $file, $filename);

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
