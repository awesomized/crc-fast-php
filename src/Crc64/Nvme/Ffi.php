<?php

declare(strict_types=1);

namespace Awesomized\Checksums\Crc64\Nvme;

use Awesomized\Checksums;

/**
 * FFI (Foreign Function Interface) helper for CRC-64/NVME checksum calculations.
 */
final class Ffi implements Checksums\FfiInterface
{
    use Checksums\FfiTrait;

    public const string SCOPE_DEFAULT = 'CRC64NVME';

    protected const string PREFIX_HEADER = 'crc64nvme';

    protected const string PREFIX_LIB = 'libcrc64fast_nvme';
}
