<?php

declare(strict_types=1);

namespace Awesomized\Checksums\Crc32\IsoHdlc;

use Awesomized\Checksums;
use FFI;
use FFI\Exception;

/**
 * A wrapper around the CRC-32/ISO-HDLC FFI library.
 *
 * This produces output compatible with crc32() and hash('crc32b') in PHP at >10X the speed.
 *
 * Input of "123456789" (no quotes) should produce a checksum of 0xCBF43926
 *
 * @link https://reveng.sourceforge.io/crc-catalogue/all.htm#crc.cat.crc-32-iso-hdlc
 */
final class Computer implements Checksums\CrcInterface
{
    use Checksums\ChecksumTrait;

    private readonly FFI $crc32IsoHdlc;

    private readonly FFI\CData $hasherHandle;

    private static ?FFI $ffiAuto = null;

    /**
     * @param FFI|null $crc32IsoHdlc The FFI instance for the CRC-32 IEEE library.
     *
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        ?FFI $crc32IsoHdlc = null,
    ) {
        $this->crc32IsoHdlc = $crc32IsoHdlc ?? self::getFfi();

        /**
         * @var FFI\CData $hasherHandle
         *
         * @psalm-suppress UndefinedMethod - from FFI, we'll catch the Exception if the method is missing
         */
        // @phpstan-ignore-next-line
        $hasherHandle = $this->crc32IsoHdlc->hasher_new();

        $this->hasherHandle = $hasherHandle;
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
        } catch (Exception $e) {
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
        } catch (Exception $e) {
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

    /**
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    protected static function getFfi(): FFI
    {
        if (null !== self::$ffiAuto) {
            return self::$ffiAuto;
        }

        return self::$ffiAuto = Checksums\Crc32\IsoHdlc\Ffi::fromAuto();
    }
}
