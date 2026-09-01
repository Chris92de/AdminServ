<?php
declare(strict_types=1);

namespace App\Services;

use App\Services\Gbx\GbxFetcher;
use RuntimeException;

class MapBrowser
{
    private string $basePath;
    private array $allowedExtensions;
    private array $hiddenFolders;

    public function __construct(string $basePath, array $allowedExtensions = ['gbx', 'map.gbx', 'challenge.gbx'], array $hiddenFolders = ['Campaigns', 'Replays'])
    {
        $this->basePath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR;
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);
        $this->hiddenFolders = $hiddenFolders;
    }

    /**
     * Resolve and validate a relative path to prevent directory traversal.
     */
    public function resolvePath(string $relativePath): string
    {
        $clean = str_replace(['../', '..\\'], '', $relativePath);
        $clean = trim($clean, '/\\');
        $fullPath = $this->basePath . ($clean !== '' ? $clean . DIRECTORY_SEPARATOR : '');
        
        $realBase = realpath($this->basePath);
        $realFull = realpath($fullPath);

        if ($realBase === false || $realFull === false || !str_starts_with($realFull, $realBase)) {
            // If path doesn't exist yet or is invalid, return sanitized path under base
            return $this->basePath . $clean;
        }

        return $realFull;
    }

    /**
     * List folders and map files inside a relative directory.
     */
    public function listDirectory(string $subDir = ''): array
    {
        $dirPath = $this->resolvePath($subDir);
        if (!is_dir($dirPath)) {
            return ['folders' => [], 'files' => [], 'current_path' => $subDir];
        }

        $folders = [];
        $files = [];

        $items = @scandir($dirPath) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullItemPath = rtrim($dirPath, '/\\') . DIRECTORY_SEPARATOR . $item;
            $relativeItemPath = trim(($subDir !== '' ? $subDir . '/' : '') . $item, '/');

            if (is_dir($fullItemPath)) {
                if (in_array($item, $this->hiddenFolders, true)) {
                    continue;
                }
                $folders[] = [
                    'name' => $item,
                    'path' => $relativeItemPath,
                ];
            } elseif (is_file($fullItemPath)) {
                $lowerName = strtolower($item);
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                $isAllowed = in_array($ext, $this->allowedExtensions, true)
                    || str_ends_with($lowerName, '.map.gbx')
                    || str_ends_with($lowerName, '.challenge.gbx')
                    || str_ends_with($lowerName, '.gbx');

                if (!$isAllowed) {
                    continue;
                }

                $gbx = new GbxFetcher($fullItemPath);
                $meta = $gbx->getData();

                $files[] = [
                    'name' => $item,
                    'path' => $relativeItemPath,
                    'size' => filesize($fullItemPath),
                    'modified_at' => filemtime($fullItemPath),
                    'uid' => $meta['uid'] ?? '',
                    'map_name' => $meta['name'] ?? $item,
                    'author' => $meta['author'] ?? 'Unknown',
                    'environment' => $meta['environment'] ?? '',
                    'mood' => $meta['mood'] ?? '',
                    'title' => $meta['title'] ?? '',
                    'type' => $meta['type'] ?? 'map',
                    'author_time' => $meta['authorTime'] ?? 0,
                    'has_thumbnail' => $meta['hasThumbnail'] ?? false,
                ];
            }
        }

        return [
            'folders' => $folders,
            'files' => $files,
            'current_path' => $subDir,
        ];
    }

    /**
     * Save an uploaded map file.
     */
    public function saveUpload(array $fileUpload, string $subDir = ''): array
    {
        if (!isset($fileUpload['tmp_name']) || !is_uploaded_file($fileUpload['tmp_name'])) {
            throw new RuntimeException("No valid upload file provided.");
        }

        $fileName = basename($fileUpload['name']);
        $lowerName = strtolower($fileName);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $isAllowed = in_array($ext, $this->allowedExtensions, true)
            || str_ends_with($lowerName, '.map.gbx')
            || str_ends_with($lowerName, '.challenge.gbx')
            || str_ends_with($lowerName, '.gbx');

        if (!$isAllowed) {
            throw new RuntimeException("Invalid map file extension. Allowed: .Map.Gbx, .Challenge.Gbx, .gbx");
        }

        $targetDir = $this->resolvePath($subDir);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($fileUpload['tmp_name'], $targetPath)) {
            throw new RuntimeException("Failed saving uploaded map to {$targetPath}");
        }

        $gbx = new GbxFetcher($targetPath);
        return [
            'success' => true,
            'filename' => $fileName,
            'path' => trim(($subDir !== '' ? $subDir . '/' : '') . $fileName, '/'),
            'meta' => $gbx->getData(),
        ];
    }
}
