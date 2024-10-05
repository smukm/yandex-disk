<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Lib;

abstract class Api
{
    public const BASE_URL = 'https://cloud-api.yandex.net/v1/disk';
    protected $lib;

    public function __construct(Lib $lib)
    {
        $this->lib = $lib;
    }
}
