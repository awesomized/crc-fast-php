<?php

declare(strict_types=1);

use Awesomized\Checksums\Crc32;

if (!isset($argv[1]) || '' === $argv[1]) {
    echo 'Usage: php calculateCrc32.php <string or file>' . PHP_EOL;

    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

$ffi = Crc32\IsoHdlc\Ffi::fromHeaderFile();

if (is_readable($argv[1])) {
    echo Crc32\IsoHdlc\Computer::calculateFile(
        ffi: $ffi,
        filename: $argv[1],
    ) . PHP_EOL;

    exit(0);
}

echo Crc32\IsoHdlc\Computer::calculate(
    ffi: $ffi,
    string: $argv[1],
) . PHP_EOL;

$contents = file_get_contents('/Users/onethumb/Downloads/frankenphp-mac-arm64');
