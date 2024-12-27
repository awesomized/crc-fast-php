<?php

declare(strict_types=1);

namespace Awesomized\Checksums;

use FFI;

/**
 * Supplies shared methods among different checksum implementations.
 */
trait ChecksumTrait
{
    public static function calculate(
        FFI $ffi,
        string $string,
    ): string {
        return (new self($ffi))
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

        $computer = new self($ffi);

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
                $computer->write(
                    string: $chunk,
                );
            }
        }

        fclose(
            stream: $handle,
        );

        return $computer->sum();
    }
}
