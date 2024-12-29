<?php

declare(strict_types=1);

namespace Awesomized\Checksums\tests\unit\Crc32\IsoHdlc;

use Awesomized\Checksums\Crc32;
use Awesomized\Checksums\tests\unit\Definitions;
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

        $ffi = Crc32\IsoHdlc\Ffi::fromCode(
            code: '',
            library: \dirname(__DIR__, 4)
            . '/build/crc32fast-lib-rust/target/'
            . Crc32\IsoHdlc\Ffi::whichLibraryTarget()
            . '/release/' . Crc32\IsoHdlc\Ffi::whichLibrary(),
        );

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $ffi->hasher_new();
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
            Crc32\IsoHdlc\Ffi::whichHeaderFile(),
        );

        if (false === $code) {
            self::markTestSkipped('Could not read the header file ' . Crc32\IsoHdlc\Ffi::whichHeaderFile());
        }

        $ffi = Crc32\IsoHdlc\Ffi::fromCode(
            code: $code,
            library: 'bogus',
        );

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $ffi->hasher_new();
    }

    /**
     * @depends testFfiFromCodeInvalidLibraryShouldFail
     *
     * @throws \InvalidArgumentException
     * @throws \FFI\Exception
     */
    public function testFfiFromHeaderInvalidHeaderShouldFail(): void
    {
        $this->expectException(Exception::class);

        $ffi = Crc32\IsoHdlc\Ffi::fromHeaderFile(
            headerFile: __DIR__ . '/FfiTest.php',
        );

        $this->testFfiCalculateCrc32ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromHeaderInvalidHeaderShouldFail
     *
     * @throws Exception
     */
    public function testFfiFromCodeValidInputShouldSucceed(): void
    {
        $code = file_get_contents(Crc32\IsoHdlc\Ffi::whichHeaderFile());
        if (false === $code) {
            self::markTestSkipped('Could not read the header file ' . Crc32\IsoHdlc\Ffi::whichHeaderFile());
        }

        $ffi = Crc32\IsoHdlc\Ffi::fromCode(
            code: $code,
            library: \dirname(__DIR__, 4)
            . '/build/crc32fast-lib-rust/target/'
            . Crc32\IsoHdlc\Ffi::whichLibraryTarget()
            . '/release/' . Crc32\IsoHdlc\Ffi::whichLibrary(),
        );

        $this->testFfiCalculateCrc32ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromCodeValidInputShouldSucceed
     *
     * @throws Exception
     * @throws \InvalidArgumentException
     */
    public function testFfiFromHeaderValidHeaderShouldSucceed(): void
    {
        $ffi = Crc32\IsoHdlc\Ffi::fromHeaderFile();

        $this->testFfiCalculateCrc32ShouldSucceed($ffi);
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
            $ffi = Crc32\IsoHdlc\Ffi::fromPreloadScope();
        } catch (\FFI\Exception $e) {
            self::markTestSkipped("FFI instance doesn't appear to be preloaded.");
        }

        $this->testFfiCalculateCrc32ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromCodeValidInputShouldSucceed
     */
    private function testFfiCalculateCrc32ShouldSucceed(
        \FFI $ffi,
    ): void {
        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $digest = $ffi->hasher_new();

        self::assertInstanceOf(\FFI\CData::class, $digest);

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $ffi->hasher_write($digest, Definitions::HELLO_WORLD, Definitions::HELLO_WORLD_LENGTH);

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        self::assertSame(
            Definitions::HELLO_WORLD_CRC32_ISO_HDLC,
            \sprintf(
                '%08x',
                // @phpstan-ignore-next-line
                $ffi->hasher_finalize($digest),
            ),
        );
    }
}
