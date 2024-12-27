<?php

declare(strict_types=1);

namespace Awesomized\Checksums\tests\unit\Crc64;

use Awesomized\Checksums\Crc64;
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

        $ffi = Crc64\Ffi::fromCode(
            code: '',
            library: __DIR__ . '/../../../build/target/release/' . Crc64\Ffi::whichLibrary(),
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
            __DIR__ . '/../../../' . Crc64\Ffi::whichHeaderFile(),
        );

        if (false === $code) {
            self::markTestSkipped('Could not read the header file ' . Crc64\Ffi::whichHeaderFile());
        }

        $ffi = Crc64\Ffi::fromCode(
            code: $code,
            library: '',
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
     */
    public function testFfiFromHeaderInvalidHeaderShouldFail(): void
    {
        $this->expectException(Exception::class);

        $ffi = Crc64\Ffi::fromHeaderFile(
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
        $code = file_get_contents(Crc64\Ffi::whichHeaderFile());
        if (false === $code) {
            self::markTestSkipped('Could not read the header file ' . Crc64\Ffi::whichHeaderFile());
        }

        $ffi = Crc64\Ffi::fromCode(
            code: $code,
            library: __DIR__ . '/../../../build/target/release/' . Crc64\Ffi::whichLibrary(),
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
        $ffi = Crc64\Ffi::fromHeaderFile();

        $this->testFfiCalculateCrc64ShouldSucceed($ffi);
    }

    /**
     * @depends testFfiFromHeaderValidHeaderShouldSucceed
     *
     * @throws Exception
     */
    public function testFfiFromPreloadScopeValidScopeShouldSucceed(): void
    {
        if (false === \ini_get('opcache.preload')) {
            self::markTestSkipped('opcache.preload is not enabled.');
        }

        $ffi = Crc64\Ffi::fromPreloadScope();

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

        self::assertInstanceOf(\FFI\CData::class, $digest);

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        $ffi->digest_write($digest, NvmeTest::HELLO_WORLD, NvmeTest::HELLO_WORLD_LENGTH);

        /**
         * @psalm-suppress UndefinedMethod - from FFI, so Psalm and PHPStan can't know if the method exists
         */
        // @phpstan-ignore-next-line
        self::assertSame(
            NvmeTest::HELLO_WORLD_CRC64,
            \sprintf(
                '%016x',
                // @phpstan-ignore-next-line
                $ffi->digest_sum64($digest),
            ),
        );
    }
}
