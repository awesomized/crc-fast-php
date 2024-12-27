<?php

declare(strict_types=1);

namespace Awesomized\Checksums;

use FFI\Exception;
use FFI\ParserException;

/**
 * FFI helper.
 *
 * Just wraps the FFI functionality to provide a more convenient API, documentation, and defaults.
 */
final class Ffi
{
    private const string SCOPE_DEFAULT = 'CRC64NVME';

    /**
     * @var array<string, \FFI>
     */
    private static array $ffis = [];

    /**
     * Creates a new FFI instance from the given C declarations and library name.
     *
     * @param string      $code    The C declarations
     * @param string|null $library The name of the shared library
     *
     * @throws ParserException
     * @throws Exception
     */
    public static function fromCode(
        string $code,
        ?string $library = null,
    ): \FFI {
        $id = $code . $library;

        if (isset(self::$ffis[$id])) {
            return self::$ffis[$id];
        }

        $ffi = \FFI::cdef(
            code: $code,
            lib: $library,
        );

        /**
         * Verify that the FFI instance is valid by calling digest_new() method before caching.
         *
         * @psalm-suppress UndefinedMethod - FFI method, can't tell if it's defined or not
         */
        // @phpstan-ignore-next-line
        $ffi->digest_new();

        return self::$ffis[$id] = $ffi;
    }

    /**
     * Creates a new FFI instance from the given preloaded scope.
     *
     * @link https://www.php.net/manual/en/opcache.preloading.php
     * @link https://www.php.net/manual/en/ffi.examples-complete.php
     *
     * @param string $ffiScopeName The FFI_SCOPE used during preloading
     */
    public static function fromPreloadScope(
        string $ffiScopeName = self::SCOPE_DEFAULT,
    ): \FFI {
        $id = $ffiScopeName;

        if (isset(self::$ffis[$id])) {
            return self::$ffis[$id];
        }

        $ffi = \FFI::scope(
            name: $ffiScopeName,
        );

        /**
         * Verify that the FFI instance is valid by calling digest_new() method before caching.
         *
         * @psalm-suppress UndefinedMethod - FFI method, can't tell if it's defined or not
         */
        // @phpstan-ignore-next-line
        $ffi->digest_new();

        return self::$ffis[$id] = $ffi;
    }

    /**
     * Creates a new FFI instance from the given C header file.
     *
     * @param string $headerFile The C header file
     *
     * @throws \InvalidArgumentException
     */
    public static function fromHeaderFile(
        string $headerFile = '',
    ): \FFI {
        if ('' === $headerFile) {
            $headerFile = self::whichHeaderFile();
        }

        $id = $headerFile;

        if (isset(self::$ffis[$id])) {
            return self::$ffis[$id];
        }

        $ffi = \FFI::load(
            filename: $headerFile,
        );

        if (null === $ffi) {
            throw new \InvalidArgumentException(
                message: 'Failed to load the FFI instance from the header file ' . $headerFile,
            );
        }

        /**
         * Verify that the FFI instance is valid by calling digest_new() method before caching.
         *
         * @psalm-suppress UndefinedMethod - FFI method, can't tell if it's defined or not
         */
        // @phpstan-ignore-next-line
        $ffi->digest_new();

        return self::$ffis[$id] = $ffi;
    }

    /**
     * Attempts to determine the correct header file for the current platform.
     *
     * @return string The path to the header file
     */
    public static function whichHeaderFile(): string
    {
        $headerFile = match (PHP_OS_FAMILY) {
            'Darwin' => 'crc64nvme-darwin.h',
            'Windows' => 'crc64nvme-windows.h',
            default => 'crc64nvme-linux.h',
        };

        return __DIR__ . '/../' . $headerFile;
    }

    public static function whichLibrary(): string
    {
        return match (PHP_OS_FAMILY) {
            'Darwin' => 'libcrc64fast_nvme.dylib',
            'Windows' => 'libcrc64fast_nvme.dll',
            default => 'libcrc64fast_nvme.so',
        };
    }
}
