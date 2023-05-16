<?php

namespace Joaociasul\PhpAsync\Helpers;

class UuidHelper
{
    public static function make(): string
    {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s_%s_%s_%s_%s%s%s', str_split(bin2hex($data), 4));
    }

}