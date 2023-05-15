<?php

namespace Joaociasul\PhpAsync\Helpers;

class FilesHelper
{
    public static function updateFileJsonAndGetOriginalContent(
        string $fileName,
        array $newContent,
        bool $rewrite = false
    ): array {
        $newContentJson = json_encode($newContent);
        if (! file_exists($fileName)) {
            self::filePutContents($fileName, $newContentJson);
            return [];
        }
        $handle = fopen($fileName, 'r+');
        if (! flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);
            return self::updateFileJsonAndGetOriginalContent($fileName, $newContent, $rewrite);
        }
        $content = self::fileGetContents($fileName);
        if (! $content || $content === '[]') {
            self::filePutContents($fileName, $newContentJson);
            fclose($handle);
            return [];
        }
        $oldContent = json_decode($content, true) ?? [];
        if (! $rewrite) {
            $newContent = array_merge($oldContent, $newContent);
            self::filePutContents($fileName, json_encode($newContent));
        } else {
            self::filePutContents($fileName, $newContentJson);
        }
        fclose($handle);
        return $oldContent;
    }

    public static function fileGetContents(
        string $filename,
        bool $useIncludePath = false,
        $context = null,
        int $offset = 0,
        ?int $length = null
    ) {
        return file_get_contents($filename, $useIncludePath, $context, $offset, $length);
    }

    public static function filePutContents(string $filename, $data, int $flags = 0, $context = null)
    {
        return file_put_contents($filename, $data, $flags, $context);
    }

}