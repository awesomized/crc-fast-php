<?php

declare(strict_types=1);

namespace Awesomized\Checksums\Crc32\IsoHdlc;

use Awesomized\Checksums;

/**
 * FFI (Foreign Function Interface) helper for CRC-32/ISO-HDLC checksum calculations.
 */
final class Ffi implements Checksums\FfiInterface
{
    use Checksums\FfiTrait;

    public const string SCOPE_DEFAULT = 'CRC32ISOHDLC';

    protected const string PREFIX_HEADER = 'crc32iso-hdlc';

    protected const string PREFIX_LIB = 'libcrc32fast_lib';
}
