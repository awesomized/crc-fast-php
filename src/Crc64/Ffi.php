<?php

declare(strict_types=1);

namespace Awesomized\Checksums\Crc64;

use Awesomized\Checksums;

/**
 * FFI (Foreign Function Interface) helper for CRC-64/NVME checksum calculations.
 */
final class Ffi implements Checksums\FfiInterface
{
    use Checksums\FfiTrait;

    public const string SCOPE_DEFAULT = 'CRC64NVME';

    public static function whichHeaderFile(): string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => 'crc64nvme-darwin.h',
            'Windows' => 'crc64nvme-windows.h',
            default => 'crc64nvme-linux.h',
        };
    }

    public static function whichLibrary(): string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => 'libcrc64fast_nvme.dylib',
            'Windows' => 'libcrc64fast_nvme.dll',
            default => 'libcrc64fast_nvme.so',
        };
    }
}
