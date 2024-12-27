<?php

declare(strict_types=1);

namespace Awesomized\Checksums\tests\unit;

use Awesomized\Checksums\Crc64;
use FFI;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

/**
 * @internal
 */
final class NvmeTest extends TestCase
{
    public const string HELLO_WORLD = 'hello, world!';
    public const int HELLO_WORLD_LENGTH = 13;
    public const string HELLO_WORLD_CRC64 = 'f8046e40c403f1d0';
    public const string HELLO_WORLD_FILE = __DIR__ . '/../fixtures/hello-world.txt';

    private FFI $ffi;

    /**
     * @throws \InvalidArgumentException
     */
    protected function setUp(): void
    {
        $this->ffi = Crc64\Ffi::fromHeaderFile();
    }

    /**
     * @throws \InvalidArgumentException
     * @throws \FFI\Exception
     */
    public function testConstructorInvalidLibraryShouldFail(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $ffi = \FFI::cdef();

        new Crc64\Nvme(
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

        $crc64Nvme = new Crc64\Nvme(
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
        $crc64 = Crc64\Nvme::calculate(
            crc64Nvme: $this->ffi,
            string: self::HELLO_WORLD,
        );

        self::assertSame(
            self::HELLO_WORLD_CRC64,
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
        $crc64 = Crc64\Nvme::calculateFile(
            crc64Nvme: $this->ffi,
            filename: self::HELLO_WORLD_FILE,
        );

        self::assertSame(
            self::HELLO_WORLD_CRC64,
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
        $crc64 = Crc64\Nvme::calculate(
            crc64Nvme: $this->ffi,
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
        $crc64Nvme = new Crc64\Nvme(
            crc64Nvme: $this->ffi,
        );

        $crc64Nvme->write('hello, ');
        $crc64Nvme->write('world!');

        self::assertSame(
            self::HELLO_WORLD_CRC64,
            $crc64Nvme->sum(),
        );
    }
}
