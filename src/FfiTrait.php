<?php

declare(strict_types=1);

namespace Awesomized\Checksums;

/**
 * FFI (Foreign Function Interface) helper Trait.
 *
 * Provides shared functionality for the FFI classes, including small performance optimizations by caching FFI
 * objects.
 */
trait FfiTrait
{
    /**
     * @var array<string, \FFI>
     */
    private static array $ffis = [];

    public static function fromAuto(): \FFI
    {
        try {
            return self::fromPreloadScope();
        } catch (\Throwable $e) {
            // ignore
        }

        return self::fromHeaderFile();
    }

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

        return self::$ffis[$id] = $ffi;
    }

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

        return self::$ffis[$id] = $ffi;
    }

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

        if (!is_readable($headerFile)) {
            throw new \InvalidArgumentException(
                message: 'Header file ' . $headerFile . ' is not readable',
            );
        }

        $ffi = \FFI::load(
            filename: $headerFile,
        );

        if (null === $ffi) {
            throw new \InvalidArgumentException(
                message: 'Failed to load the FFI instance from the header file ' . $headerFile,
            );
        }

        return self::$ffis[$id] = $ffi;
    }

    public static function whichHeaderFile(): string
    {
        $headerFile = self::PREFIX_HEADER
            . '-'
            . php_uname('m')
            . '-'
            . strtolower(PHP_OS_FAMILY)
            . '.h';

        // default non-vendor context
        $headerDirectory = \dirname(__DIR__);

        if (
            1 === preg_match(
                pattern: '/(.*)\/vendor\/([^\/]+\/[^\/]+)\/src/',
                subject: __DIR__,
                matches: $matches,
            )
        ) {
            // in a vendor context
            $headerDirectory = $matches[1];
        }

        return $headerDirectory . '/include/' . $headerFile;
    }

    public static function whichLibrary(): string
    {
        // TODO: add more library names as needed
        return match (PHP_OS_FAMILY) {
            self::OS_DARWIN => self::PREFIX_LIB . '.dylib',
            self::OS_WINDOWS => self::PREFIX_LIB . '.dll',
            default => self::PREFIX_LIB . '.so',
        };
    }

    public static function whichLibraryTarget(): string
    {
        // TODO: add more targets as needed
        return match (php_uname('m')) {
            'aarch64', 'arm64' => 'aarch64',
            default => 'x86_64',
        }
        . '-'
        . match (PHP_OS_FAMILY) {
            self::OS_DARWIN => 'apple-darwin',
            self::OS_WINDOWS => 'pc-windows-gnu',
            default => 'unknown-linux-gnu',
        };
    }
}
