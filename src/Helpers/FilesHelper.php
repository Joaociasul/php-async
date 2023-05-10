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
            file_put_contents($fileName, $newContentJson);
            return [];
        }
        $handle = fopen($fileName, 'r+');
        if (! flock($handle, LOCK_EX | LOCK_NB)) {
            fclose($handle);
            return self::updateFileJsonAndGetOriginalContent($fileName, $newContent, $rewrite);
        }
        $content = file_get_contents($fileName);
        if (! $content || $content === '[]') {
            file_put_contents($fileName, $newContentJson);
            fclose($handle);
            return [];
        }
        $oldContent = json_decode($content, true) ?? [];
        if (! $rewrite) {
            $newContent = array_merge($oldContent, $newContent);
            file_put_contents($fileName, json_encode($newContent));
        } else {
            file_put_contents($fileName, $newContentJson);
        }
        fclose($handle);
        return $oldContent;
    }

}