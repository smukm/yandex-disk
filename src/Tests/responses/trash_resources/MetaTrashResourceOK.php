<?php

namespace smukm\YandexDisk\Tests\responses\trash_resources;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class MetaTrashResourceOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $body = '{
          "preview": "https://downloader.disk.yandex.ru/preview/...",
          "name": "cat.png",
          "created": "2014-07-16T13:07:45+04:00",
          "custom_properties": {"foo":"1", "bar":"2"},
          "origin_path": "disk:/foo/cat.png",
          "modified": "2014-07-16T13:07:45+04:00",
          "path": "trash:/cat.png",
          "md5": "02bab05c02537e53dedd408261e0aadf",
          "type": "file",
          "mime_type": "image/png",
          "size": 903337
        }';

        return $this->response(200, $body);
    }
}
