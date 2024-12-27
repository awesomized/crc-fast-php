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

use Awesomized\Checksums\Crc64;

require 'src/Ffi.php';

\FFI::load(
    Crc64\Ffi::whichHeaderFile(),
);
