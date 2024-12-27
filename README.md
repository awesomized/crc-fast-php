# crc64nvme
[![Code Standards](https://github.com/awesomized/crc64nvme/actions/workflows/code-standards.yml/badge.svg?branch=main)](https://github.com/awesomized/crc64nvme/actions/workflows/code-standards.yml)
[![Static Analysis](https://github.com/awesomized/crc64nvme/actions/workflows/static-analysis.yml/badge.svg?branch=main)](https://github.com/awesomized/crc64nvme/actions/workflows/static-analysis.yml)

Fast, SIMD-accelerated `CRC-64/NVME` computation in PHP via FFI using the [crc64fast-nvme](https://github.com/awesomized/crc64fast-nvme) Rust package and its C-compatible shared library.

It's capable of generating checksums at >20-50 GiB/s, depending on the CPU. It is much, much faster (>100X) than the native [crc32](https://www.php.net/manual/en/function.crc32.php), crc32b, and crc32c [implementations](https://www.php.net/manual/en/function.hash-algos.php) in PHP.

`CRC-64/NVME` is in use in a variety of large-scale and mission-critical systems, software, and hardware, such as:
- AWS S3's [recommended checksum](https://docs.aws.amazon.com/AmazonS3/latest/userguide/checking-object-integrity.html)
- The [Linux kernel](https://github.com/torvalds/linux/blob/786c8248dbd33a5a7a07f7c6e55a7bfc68d2ca48/lib/crc64.c#L66-L73)
- The [NVMe specification](https://nvmexpress.org/wp-content/uploads/NVM-Express-NVM-Command-Set-Specification-1.0d-2023.12.28-Ratified.pdf)

## Changes

See the [change log](CHANGELOG.md).

## Requirements

You'll need to have built and installed the [crc64fast-nvme](https://github.com/awesomized/crc64fast-nvme) shared Rust library, and possibly configured where and how to load it. (See [Usage](#Usage), below).

## Installation

Use [Composer](https://getcomposer.org) to install this library (note the [Requirements](#Requirements) above):

```bash
composer require awesomized/crc64nvme
```


## Usage

### Creating the CRC-64/NVME FFI object 

A [helper FFI Class](src/Ffi.php) is provided, which supplies many ways to easily create an FFI object for the [crc64fast-nvme](https://github.com/awesomized/crc64fast-nvme) shared library:

#### - Via [preloaded](https://www.php.net/manual/en/ffi.examples-complete.php) shared library (recommended for any long-running workloads, such as web requests):
```php
use Awesomized\Checksums\Crc64;

// uses the opcache preloaded shared library and PHP Class(es)
$crc64Nvme = Crc64\Ffi::fromPreloadedScope(
    scope: 'CRC64NVME', // optional, this is the default
);
```

#### - Via a C header file:
Uses a C header file to define the functions and point to the shared library (`.so` on Linux, `.dll` on Windows, `.dylib` on macOS, etc).

```php
use Awesomized\Checksums\Crc64;

// uses the FFI_LIB and FFI_SCOPE definitions in the header file
$crc64Nvme = Crc64\Ffi::fromHeaderFile(
    headerFile: 'path/to/crc64fast_nvme.h', // optional, can likely be inferred from the OS
);
```

#### - Via C definitions + library:
```php
use Awesomized\Checksums\Crc64;

// uses the supplied C definitions and name/location of the shared library
$crc64Nvme = Crc64\Ffi::fromCode(
    code: 'typedef struct DigestHandle DigestHandle;
            DigestHandle* digest_new(void);
            void digest_write(DigestHandle* handle, const char* data, size_t len);
            uint64_t digest_sum64(const DigestHandle* handle);
            void digest_free(DigestHandle* handle);',
    library: 'libcrc64fast_nvme.so',
);
```
### Using the CRC-64/NVME FFI object

#### Calculate CRC-64/NVME checksums:
```php
use Awesomized\Checksums\Crc64;

/** @var \FFI $crc64Fast */

// calculate the checksum of a string
$checksum = Crc64\Nvme::calculate(
    crc64Nvme: $crc64Fast, 
    string: 'hello, world!'
); // f8046e40c403f1d0

// calculate the checksum of a file, which will chunk through the file optimally,
// limiting RAM usage and maximizing throughput
$checksum = Crc64\Nvme::calculateFile(
    crc64Nvme: $crc64Fast, 
    filename: 'path/to/hello-world'
); // f8046e40c403f1d0
```

#### Calculate CRC-64/NVME checksums with a Digest for intermittent / streaming / etc workloads:
```php
use Awesomized\Checksums\Crc64;

/** @var \FFI $crc64FastNvme */

$crc64Digest = new Crc64\Nvme(
    crc64Nvme: $crc64FastNvme,
);

// write some data to the digest
$crc64Digest->write('hello,');

// write some more data to the digest
$crc64Digest->write(' world!');

// calculate the entire digest
$checksum = $crc64Digest->sum(); // f8046e40c403f1d0
```

## Examples

There's a sample [CLI script](cli/calculate.php) that demonstrates how to use this library, or quickly calculate some checksums

## Development

This project uses [SemVer](https://semver.org), and has extensive coding standards, static analysis, and test coverage tooling. See the [Makefile](Makefile) for details.

Examples:

#### Building the shared `crc64fast-nvme` Rust library
```bash
make build
``` 
#### Validating PHP code
```bash
make validate
``` 

#### Repairing PHP code quality issues
```bash
make repair
```

Pull requests for improvements welcome.

## Testing

There's a [test suite](tests/) with `unit` test coverage.

#### Running the tests

```bash
make test
```
