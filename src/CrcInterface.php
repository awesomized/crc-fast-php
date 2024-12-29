<?php

declare(strict_types=1);

namespace Awesomized\Checksums;

use Awesomized\Checksums;
use FFI;
use FFI\Exception;

/**
 * A common Interface for CRC checksum calculations via FFI (Foreign Function Interface) libraries.
 *
 * @see Checksums\Crc64\Nvme\Computer
 * @see Checksums\Crc32\IsoHdlc\Computer
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
     * @param string   $string The string to calculate the CRC checksum for.
     * @param FFI|null $ffi    The FFI instance for the CRC library.
     *
     * @return string The calculated CRC checksum as a hexadecimal string (due to signed large int issues in PHP for
     *                64-bit results).
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws Exception
     */
    public static function calculate(
        string $string,
        ?FFI $ffi = null,
    ): string;

    /**
     * Calculates the CRC checksum for a file.
     *
     * @param string      $filename      The file or URL.
     * @param int<1, max> $readChunkSize The size of the chunks to read from the file. Adjust as necessary for your
     *                                   environment.
     * @param FFI|null    $ffi           The FFI instance for the CRC library.
     *
     * @return string The calculated CRC checksum as a hexadecimal string (due to signed large int issues in PHP for
     *                64-bit results).
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws Exception
     */
    public static function calculateFile(
        string $filename,
        int $readChunkSize = self::READ_CHUNK_SIZE_DEFAULT,
        ?FFI $ffi = null,
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
