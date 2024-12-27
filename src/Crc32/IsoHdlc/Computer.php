<?php

declare(strict_types=1);

namespace Awesomized\Checksums\Crc32\IsoHdlc;

use Awesomized\Checksums;
use FFI;

/**
 * A wrapper around the CRC-32/ISO-HDLC FFI library.
 *
 * @link https://reveng.sourceforge.io/crc-catalogue/all.htm#crc.cat.crc-32-iso-hdlc
 */
final class Computer implements Checksums\CrcInterface
{
    use Checksums\ChecksumTrait;

    private FFI\CData $hasherHandle;

    /**
     * @param FFI $crc32IsoHdlc The FFI instance for the CRC-32 IEEE library.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        private readonly FFI $crc32IsoHdlc,
    ) {
        try {
            /**
             * @var FFI\CData $digestHandle
             *
             * @psalm-suppress UndefinedMethod - from FFI, we'll catch the Exception if the method is missing
             */
            // @phpstan-ignore-next-line
            $digestHandle = $this->crc32IsoHdlc->hasher_new();
        } catch (FFI\Exception $e) {
            throw new \InvalidArgumentException(
                message: 'Could not create a new Hasher handle.'
                . ' Is the library loaded, and has the hasher_new() method?',
                previous: $e,
            );
        }

        $this->hasherHandle = $digestHandle;
    }

    public function write(
        string $string,
    ): self {
        try {
            /** @psalm-suppress UndefinedMethod - already checked this in the ctor */
            // @phpstan-ignore-next-line
            $this->crc32IsoHdlc->hasher_write(
                $this->hasherHandle,
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
             * @var int $crc32
             *
             * @psalm-suppress UndefinedMethod - already checked this in the ctor
             */
            // @phpstan-ignore-next-line
            $crc32 = $this->crc32IsoHdlc->hasher_finalize(
                $this->hasherHandle,
            );
        } catch (FFI\Exception $e) {
            throw new \RuntimeException(
                message: 'Could not calculate the CRC-32 checksum. '
                . ' Is the library loaded, and has the hasher_finalize() method?',
                previous: $e,
            );
        }

        return \sprintf(
            '%08x',
            $crc32,
        );
    }
}
