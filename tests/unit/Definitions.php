<?php

declare(strict_types=1);

namespace Awesomized\Checksums\tests\unit;

abstract class Definitions
{
    public const string CHECK_INPUT = '123456789';

    /**
     * @link https://reveng.sourceforge.io/crc-catalogue/all.htm#crc.cat.crc-32-iso-hdlc
     */
    public const string CHECK_RESULT_CRC32_ISO_HDLC = 'cbf43926';

    /**
     * @link https://reveng.sourceforge.io/crc-catalogue/all.htm#crc.cat.crc-64-nvme
     */
    public const string CHECK_RESULT_CRC64_NVME = 'ae8b14860a799888';

    public const string HELLO_WORLD = 'hello, world!';
    public const int HELLO_WORLD_LENGTH = 13;
    public const string HELLO_WORLD_CRC32_ISO_HDLC = '58988d13';
    public const string HELLO_WORLD_CRC64_NVME = 'f8046e40c403f1d0';
    public const string HELLO_WORLD_FILE = __DIR__ . '/../fixtures/hello-world.txt';
}
