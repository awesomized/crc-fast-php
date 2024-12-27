<?php

declare(strict_types=1);

namespace Awesomized\Checksums\tests\unit\Crc64\Nvme;

use Awesomized\Checksums\Crc64;
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
        $this->ffi = Crc64\Nvme\Ffi::fromHeaderFile();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \FFI\Exception
     */
    public function testConstructorInvalidLibraryShouldFail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $ffi = \FFI::cdef();

        new Crc64\Nvme\Computer(
            crc64Nvme: $ffi,
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

        $crc64Nvme = new Crc64\Nvme\Computer(
            crc64Nvme: $this->ffi,
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
        $crc64 = Crc64\Nvme\Computer::calculate(
            ffi: $this->ffi,
            string: Definitions::HELLO_WORLD,
        );

        self::assertSame(
            Definitions::HELLO_WORLD_CRC64_NVME,
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
        $crc64 = Crc64\Nvme\Computer::calculateFile(
            ffi: $this->ffi,
            filename: Definitions::HELLO_WORLD_FILE,
        );

        self::assertSame(
            Definitions::HELLO_WORLD_CRC64_NVME,
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
        $crc64 = Crc64\Nvme\Computer::calculate(
            ffi: $this->ffi,
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
        $crc64Nvme = new Crc64\Nvme\Computer(
            crc64Nvme: $this->ffi,
        );

        $crc64Nvme->write('hello, ');
        $crc64Nvme->write('world!');

        self::assertSame(
            Definitions::HELLO_WORLD_CRC64_NVME,
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
        $crc64 = Crc64\Nvme\Computer::calculate(
            ffi: $this->ffi,
            string: Definitions::CHECK_INPUT,
        );

        self::assertSame(
            Definitions::CHECK_RESULT_CRC64_NVME,
            $crc64,
        );
    }
}
