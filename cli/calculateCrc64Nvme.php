<?php

declare(strict_types=1);

use Awesomized\Checksums\Crc64;

if (!isset($argv[1]) || '' === $argv[1]) {
    echo 'Usage: php calculateCrc64Nvme.php <string or file>' . PHP_EOL;

    exit(1);
}

require __DIR__ . '/../vendor/autoload.php';

$ffi = Crc64\Nvme\Ffi::fromHeaderFile();

if (is_readable($argv[1])) {
    echo Crc64\Nvme\Computer::calculateFile(
        ffi: $ffi,
        filename: $argv[1],
    ) . PHP_EOL;

    exit(0);
}

echo Crc64\Nvme\Computer::calculate(
    ffi: $ffi,
    string: $argv[1],
) . PHP_EOL;
