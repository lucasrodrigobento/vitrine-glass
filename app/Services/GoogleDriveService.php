<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    private const CACHE_TTL  = 6 * 60 * 60; // 6 horas
    private const PAGE_SIZE  = 1000;
    private const FIELDS     = 'nextPageToken, files(id, name)';
    private const MIME_QUERY = "mimeType contains 'image/' and trashed = false";

    /**
     * Retorna todas as imagens de uma pasta do Drive, com cache de 6h.
     * Cada item: ['id' => '...', 'name' => '...']
     */
    public function catalogImages(string $tenantSlug, string $apiKey, string $folderId): array
    {
        return Cache::remember(
            "drive_catalog:{$tenantSlug}",
            self::CACHE_TTL,
            fn () => $this->fetchAll($apiKey, $folderId)
        );
    }

    public function clearCache(string $tenantSlug): void
    {
        Cache::forget("drive_catalog:{$tenantSlug}");
    }

    public function thumbUrl(string $id, int $width = 800): string
    {
        return "https://drive.google.com/thumbnail?id={$id}&sz=w{$width}";
    }

    public function fullUrl(string $id): string
    {
        return "https://drive.google.com/uc?export=view&id={$id}";
    }

    private function fetchAll(string $apiKey, string $folderId): array
    {
        $all       = [];
        $pageToken = null;

        do {
            $params = [
                'q'        => "'{$folderId}' in parents and " . self::MIME_QUERY,
                'fields'   => self::FIELDS,
                'pageSize' => self::PAGE_SIZE,
                'key'      => $apiKey,
            ];
            if ($pageToken) {
                $params['pageToken'] = $pageToken;
            }

            try {
                $resp = Http::timeout(10)->get('https://www.googleapis.com/drive/v3/files', $params);
            } catch (\Throwable $e) {
                Log::warning("GoogleDriveService: request failed — {$e->getMessage()}");
                break;
            }

            if (! $resp->successful()) {
                Log::warning("GoogleDriveService: API error {$resp->status()} — " . $resp->body());
                break;
            }

            $data = $resp->json();
            $all  = array_merge($all, $data['files'] ?? []);

            $pageToken = $data['nextPageToken'] ?? null;
        } while ($pageToken);

        return $all;
    }
}
