<?php

declare(strict_types=1);

namespace Awesomized\Checksums;

use FFI;

/**
 * A common Interface for CRC checksum calculations via FFI (Foreign Function Interface) libraries.
 *
 * @see Crc64\Nvme
 * @link https://www.php.net/manual/en/book.ffi.php
 */
interface CrcInterface
{
    /**
     * The default read chunk size for file checksum calculation.
     *
     * 512 KiB turned out to be the fastest in my test cases.
     */
    public const int READ_CHUNK_SIZE_DEFAULT = 524288;

    /**
     * Calculates the CRC checksum for a string.
     *
     * @param FFI    $ffi    The FFI instance for the CRC library.
     * @param string $string The string to calculate the CRC checksum for.
     *
     * @return string The calculated CRC checksum as a hexadecimal string (due to signed large int issues in PHP for
     *                64-bit results).
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function calculate(
        FFI $ffi,
        string $string,
    ): string;

    /**
     * Calculates the CRC checksum for a file.
     *
     * @param FFI         $ffi           The FFI instance for the CRC library.
     * @param string      $filename      The file or URL.
     * @param int<1, max> $readChunkSize The size of the chunks to read from the file. Adjust as necessary for your
     *                                   environment.
     *
     * @return string The calculated CRC checksum as a hexadecimal string (due to signed large int issues in PHP for
     *                64-bit results).
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public static function calculateFile(
        FFI $ffi,
        string $filename,
        int $readChunkSize = self::READ_CHUNK_SIZE_DEFAULT,
    ): string;

    /**
     * Writes a string to the CRC checksum calculation digest.
     *
     * @throws \RuntimeException
     */
    public function write(
        string $string,
    ): self;

    /**
     * Returns the calculated CRC checksum as a hexadecimal string (due to signed large int issues in PHP for 64-bit
     * results).
     *
     * @throws \RuntimeException
     */
    public function sum(): string;
}
