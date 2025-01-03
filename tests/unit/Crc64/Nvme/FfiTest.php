<?php

declare(strict_types=1);

namespace Awesomized\Checksums\tests\unit\Crc64\Nvme;

use Awesomized\Checksums\Crc64;
use Awesomized\Checksums\tests\unit\Definitions;
use FFI\CData;
use FFI\Exception;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class FfiTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testFfiFromCodeInvalidCodeShouldFail(): void
    {
        $this->expectException(Exception::class);

        $ffi = Crc64\Nvme\Ffi::fromCode(
            code: '',
            library: \dirname(__DIR__, 4)
            . '/build/crc64fast-nvme/target/'
            . Crc64\Nvme\Ffi::whichLibraryTarget()
            . '/release/' . Crc64\Nvme\Ffi::whichLibrary(),
        );

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $ffi->digest_new();
    }

    /**
     * @depends testFfiFromCodeInvalidCodeShouldFail
     *
     * @throws Exception
     */
    public function testFfiFromCodeInvalidLibraryShouldFail(): void
    {
        $this->expectException(Exception::class);

        $code = file_get_contents(
            Crc64\Nvme\Ffi::whichHeaderFile(),
        );

        if (false === $code) {
            self::markTestSkipped('Could not read the header file ' . Crc64\Nvme\Ffi::whichHeaderFile());
        }

        $ffi = Crc64\Nvme\Ffi::fromCode(
            code: $code,
            library: 'bogus',
        );

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $ffi->digest_new();
    }

    /**
     * @depends testFfiFromCodeInvalidLibraryShouldFail
     *
     * @throws \InvalidArgumentException
     * @throws Exception
     */
    public function testFfiFromHeaderInvalidHeaderShouldFail(): void
    {
        $this->expectException(Exception::class);

        $ffi = Crc64\Nvme\Ffi::fromHeaderFile(
            headerFile: __DIR__ . '/FfiTest.php',
        );

        $this->testFfiCalculateCrc64ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromHeaderInvalidHeaderShouldFail
     *
     * @throws Exception
     */
    public function testFfiFromCodeValidInputShouldSucceed(): void
    {
        $code = file_get_contents(Crc64\Nvme\Ffi::whichHeaderFile());
        if (false === $code) {
            self::markTestSkipped('Could not read the header file ' . Crc64\Nvme\Ffi::whichHeaderFile());
        }

        $ffi = Crc64\Nvme\Ffi::fromCode(
            code: $code,
            library: \dirname(__DIR__, 4)
            . '/build/crc64fast-nvme/target/'
            . Crc64\Nvme\Ffi::whichLibraryTarget()
            . '/release/' . Crc64\Nvme\Ffi::whichLibrary(),
        );

        $this->testFfiCalculateCrc64ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromCodeValidInputShouldSucceed
     *
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testFfiFromHeaderValidHeaderShouldSucceed(): void
    {
        $ffi = Crc64\Nvme\Ffi::fromHeaderFile();

        $this->testFfiCalculateCrc64ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromHeaderValidHeaderShouldSucceed
     *
     * @throws Exception
     */
    public function testFfiFromPreloadScopeValidScopeShouldSucceed(): void
    {
        $opcachePreload = \ini_get('opcache.preload');
        if (false === $opcachePreload || '' === $opcachePreload) {
            self::markTestSkipped('opcache.preload is not enabled.');
        }

        try {
            $ffi = Crc64\Nvme\Ffi::fromPreloadScope();
        } catch (Exception $e) {
            self::markTestSkipped("FFI instance doesn't appear to be preloaded.");
        }

        $this->testFfiCalculateCrc64ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromCodeValidInputShouldSucceed
     */
    private function testFfiCalculateCrc64ShouldSucceed(
        \FFI $ffi,
    ): void {
        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $digest = $ffi->digest_new();

        self::assertInstanceOf(CData::class, $digest);

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $ffi->digest_write(
            $digest,
            Definitions::HELLO_WORLD,
            Definitions::HELLO_WORLD_LENGTH,
        );

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        self::assertSame(
            Definitions::HELLO_WORLD_CRC64_NVME,
            \sprintf(
                '%016x',
                // @phpstan-ignore-next-line
                $ffi->digest_sum64($digest),
            ),
        );
    }
}
