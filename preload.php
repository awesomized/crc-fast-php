<?php

/**
 * Script suitable for preloading the FFI library.
 *
 * @link https://www.php.net/manual/en/opcache.preloading.php
 * @link https://www.php.net/manual/en/ffi.examples-complete.php
 *
 * @see \Awesome\Checksums\Crc64\Ffi::fromPreloadScope()
 */

declare(strict_types=1);

use Awesomized\Checksums;

require 'src/FfiInterface.php';
require 'src/FfiTrait.php';
require 'src/Crc64/Nvme/Ffi.php';
require 'src/Crc32/IsoHdlc/Ffi.php';

\FFI::load(
    Checksums\Crc64\Nvme\Ffi::whichHeaderFile(),
);

\FFI::load(
    Checksums\Crc32\IsoHdlc\Ffi::whichHeaderFile(),
);
