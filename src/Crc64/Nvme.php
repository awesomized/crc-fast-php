<?php

declare(strict_types=1);

namespace Awesomized\Checksums\Crc64;

use Awesomized\Checksums;
use FFI;

/**
 * A wrapper around the CRC-64 NVMe FFI library.
 *
 * @see  \Awesomized\Checksums\Crc64\Ffi
 * @link https://github.com/awesomized/crc64fast-nvme
 */
final class Nvme implements Checksums\CrcInterface
{
    private FFI\CData $digestHandle;

    /**
     * @param FFI $crc64Nvme The FFI instance for the CRC-64 NVMe library.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly FFI $crc64Nvme,
    ) {
        try {
            /**
             * @var FFI\CData $digestHandle
             *
             * @psalm-suppress UndefinedMethod - from FFI, we'll catch the Exception if the method is missing
             */
            // @phpstan-ignore-next-line
            $digestHandle = $this->crc64Nvme->digest_new();
        } catch (FFI\Exception $e) {
            throw new \InvalidArgumentException(
                message: 'Could not create a new Digest handle.'
                . ' Is the library loaded, and has the digest_new() method?',
                previous: $e,
            );
        }

        $this->digestHandle = $digestHandle;
    }

    public static function calculate(
        FFI $ffi,
        string $string,
    ): string {
        return (new self(
            crc64Nvme: $ffi,
        ))
            ->write(
                string: $string,
            )
            ->sum();
    }

    public static function calculateFile(
        FFI $ffi,
        string $filename,
        int $readChunkSize = self::READ_CHUNK_SIZE_DEFAULT,
    ): string {
        $handle = fopen(
            filename: $filename,
            mode: 'rb',
        );

        if (false === $handle) {
            throw new \InvalidArgumentException(
                message: "Could not open file: {$filename}",
            );
        }

        $nvme = new self(
            crc64Nvme: $ffi,
        );

        while (
            !feof(
                stream: $handle,
            )
        ) {
            $chunk = fread(
                stream: $handle,
                length: $readChunkSize,
            );
            if (false !== $chunk) {
                $nvme->write(
                    string: $chunk,
                );
            }
        }

        fclose(
            stream: $handle,
        );

        return $nvme->sum();
    }

    public function write(
        string $string,
    ): self {
        try {
            /** @psalm-suppress UndefinedMethod - already checked this in the ctor */
            // @phpstan-ignore-next-line
            $this->crc64Nvme->digest_write(
                $this->digestHandle,
                $string,
                \strlen($string),
            );
        } catch (FFI\Exception $e) {
            throw new \RuntimeException(
                message: 'Could not write to the Digest handle. '
                . 'Is the library loaded, and has the digest_write() method?',
                previous: $e,
            );
        }

        return $this;
    }

    public function sum(): string
    {
        try {
            /**
             * @var int $crc64
             *
             * @psalm-suppress UndefinedMethod - already checked this in the ctor
             */
            // @phpstan-ignore-next-line
            $crc64 = $this->crc64Nvme->digest_sum64(
                $this->digestHandle,
            );
        } catch (FFI\Exception $e) {
            throw new \RuntimeException(
                message: 'Could not calculate the CRC-64 checksum. '
                . ' Is the library loaded, and has the digest_sum64() method?',
                previous: $e,
            );
        }

        return \sprintf(
            '%016x',
            $crc64,
        );
    }
}
