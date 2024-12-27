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

        $ffi = \FFI::load(
            filename: __DIR__ . '/../' . $headerFile,
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
        return match (PHP_OS_FAMILY) {
            self::OS_DARWIN => self::PREFIX_HEADER . '-darwin.h',
            self::OS_WINDOWS => self::PREFIX_HEADER . '-windows.h',
            default => self::PREFIX_HEADER . '-linux.h',
        };
    }

    public static function whichLibrary(): string
    {
        return match (PHP_OS_FAMILY) {
            self::OS_DARWIN => self::PREFIX_LIB . '.dylib',
            self::OS_WINDOWS => self::PREFIX_LIB . '.dll',
            default => self::PREFIX_LIB . '.so',
        };
    }
}
