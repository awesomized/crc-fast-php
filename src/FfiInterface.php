<?php

declare(strict_types=1);

namespace Awesomized\Checksums;

use FFI\Exception;
use FFI\ParserException;

/**
 * FFI (Foreign Function Interface) helper Interface.
 *
 * Just wraps the FFI functionality to provide a more convenient API, documentation, and defaults.
 *
 * @link https://www.php.net/manual/en/book.ffi.php
 */
interface FfiInterface
{
    // override in children
    public const string SCOPE_DEFAULT = 'SCOPE_DEFAULT';

    public const string OS_DARWIN = 'Darwin';
    public const string OS_WINDOWS = 'Windows';

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
    ): \FFI;

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
    ): \FFI;

    /**
     * Creates a new FFI instance from the given C header file.
     *
     * @param string $headerFile The C header file
     *
     * @throws \InvalidArgumentException
     */
    public static function fromHeaderFile(
        string $headerFile = '',
    ): \FFI;

    /**
     * Attempts to determine the correct header file for the current platform.
     *
     * @return string The name of the header file
     */
    public static function whichHeaderFile(): string;

    /**
     * Attempts to determine the correct library for the current platform.
     *
     * @return string The name of the shared library
     */
    public static function whichLibrary(): string;

    /**
     * Attempts to determine the correct library target for the current platform (e.g. 'aarch64-unknown-linux-gnu').
     *
     * @return string The target of the shared library for this platform
     */
    public static function whichLibraryTarget(): string;
}
