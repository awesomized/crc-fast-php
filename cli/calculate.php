<?php

declare(strict_types=1);

use Awesomized\Checksums\Crc64;

if (!isset($argv[1]) || '' === $argv[1]) {
    echo 'Usage: php calculateString.php <string or file>' . PHP_EOL;

    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

$ffi = Crc64\Ffi::fromHeaderFile(
    headerFile: Crc64\Ffi::whichHeaderFile(),
);

if (is_readable($argv[1])) {
    echo Crc64\Nvme::calculateFile(
        crc64Nvme: $ffi,
        filename: $argv[1],
    ) . PHP_EOL;

    exit(0);
}

echo Crc64\Nvme::calculate(
    crc64Nvme: $ffi,
    string: $argv[1],
) . PHP_EOL;
