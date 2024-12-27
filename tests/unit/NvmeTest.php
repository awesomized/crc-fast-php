<?php

declare(strict_types=1);

namespace Awesomized\Checksums\Crc64\tests\unit;

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
        try {
            if (false !== \ini_get('opcache.preload')) {
                $this->ffi = Crc64\Ffi::fromPreloadScope();

                return;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $this->ffi = Crc64\Ffi::fromHeaderFile();
    }

    /**
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
     * @throws \InvalidArgumentException
     * @throws \FFI\Exception
     */
    public function testConstructorShouldFailWithInvalidLibrary(): void
    {
        $code = file_get_contents(Crc64\Ffi::whichHeaderFile());
        if (false === $code) {
            self::markTestSkipped('Could not read the header file ' . Crc64\Ffi::whichHeaderFile());
        }

        $this->expectException(\FFI\Exception::class);

        new Crc64\Nvme(
            crc64Nvme: Crc64\Ffi::fromCode(
                code: $code,
                library: '',
            ),
        );
    }
}
