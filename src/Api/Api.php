<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Lib;

abstract class Api
{
    public const BASE_URL = 'https://cloud-api.yandex.net/v1/disk';

    public function __construct(
        protected Lib $lib
    )
    {}
}
