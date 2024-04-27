<?php

namespace smukm\YandexDisk\Tests\responses\resources;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class CreateOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $body = '{
          "href": "https://cloud-api.yandex.net/v1/disk/resources?path=disk%3A%2FMusic",
          "method": "GET",
          "templated": false
        }';
        return $this->response(201, $body);
    }
}
