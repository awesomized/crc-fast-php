<?php

declare(strict_types=1);

namespace Awesomized\Checksums\tests\unit\Crc32\IsoHdlc;

use Awesomized\Checksums\Crc32;
use Awesomized\Checksums\tests\unit\Definitions;
use FFI;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

/**
 * @internal
 */
final class ComputerTest extends TestCase
{
    private FFI $ffi;

    /**
     * @throws \InvalidArgumentException
     */
    protected function setUp(): void
    {
        $this->ffi = Crc32\IsoHdlc\Ffi::fromAuto();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \FFI\Exception
     */
    public function testConstructorInvalidLibraryShouldFail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $ffi = \FFI::cdef();

        new Crc32\IsoHdlc\Computer(
            crc32IsoHdlc: $ffi,
        );
    }

    /**
     * @depends testConstructorInvalidLibraryShouldFail
     *
     * @throws \InvalidArgumentException
     */
    public function testConstructorValidLibraryShouldSucceed(): void
    {
        $this->expectNotToPerformAssertions();

        $ffi = new Crc32\IsoHdlc\Computer(
            crc32IsoHdlc: $this->ffi,
        );
    }

    /**
     * @depends testConstructorValidLibraryShouldSucceed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function testCalculateHelloWorldShouldSucceed(): void
    {
        $crc64 = Crc32\IsoHdlc\Computer::calculate(
            string: Definitions::HELLO_WORLD,
        );

        self::assertSame(
            Definitions::HELLO_WORLD_CRC32_ISO_HDLC,
            $crc64,
        );
    }

    /**
     * @depends testConstructorValidLibraryShouldSucceed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function testCalculateFileHelloWorldShouldSucceed(): void
    {
        $crc64 = Crc32\IsoHdlc\Computer::calculateFile(
            filename: Definitions::HELLO_WORLD_FILE,
        );

        self::assertSame(
            Definitions::HELLO_WORLD_CRC32_ISO_HDLC,
            $crc64,
        );
    }

    /**
     * Ensure that binary data is calculated properly, especially null bytes (0x00), which has been problematic in the
     * past.
     *
     * @depends testConstructorValidLibraryShouldSucceed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws RandomException
     */
    public function testCalculateBinaryDataShouldSucceed(): void
    {
        $crc64 = Crc32\IsoHdlc\Computer::calculate(
            string: 0x00 . random_bytes(1024 * 1024),
        );

        self::assertNotSame('0000000000000000', $crc64);
    }

    /**
     * @depends testConstructorValidLibraryShouldSucceed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function testCalculateChunkedDataShouldSucceed(): void
    {
        $crc64Nvme = new Crc32\IsoHdlc\Computer();

        $crc64Nvme->write('hello, ');
        $crc64Nvme->write('world!');

        self::assertSame(
            Definitions::HELLO_WORLD_CRC32_ISO_HDLC,
            $crc64Nvme->sum(),
        );
    }

    /**
     * @depends testConstructorValidLibraryShouldSucceed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function testCalculateCheckValueShouldMatch(): void
    {
        $crc32 = Crc32\IsoHdlc\Computer::calculate(
            string: Definitions::CHECK_INPUT,
        );

        self::assertSame(
            Definitions::CHECK_RESULT_CRC32_ISO_HDLC,
            $crc32,
        );
    }

    /**
     * @depends testConstructorValidLibraryShouldSucceed
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function testComparePhpFunctionKnownValuesShouldMatch(): void
    {
        self::assertSame(
            dechex(crc32(Definitions::HELLO_WORLD)),
            Crc32\IsoHdlc\Computer::calculate(
                string: Definitions::HELLO_WORLD,
            ),
        );

        self::assertSame(
            dechex(crc32(Definitions::CHECK_INPUT)),
            Crc32\IsoHdlc\Computer::calculate(
                string: Definitions::CHECK_INPUT,
            ),
        );
    }
}
