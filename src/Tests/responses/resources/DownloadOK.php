<?php

namespace smukm\YandexDisk\Tests\responses\resources;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class DownloadOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        if($uri->getPath() === '/v1/disk/resources/download') {
            $body = '{
              "href": "https://downloader.dst.yandex.ru/disk/test",
              "method": "GET",
              "templated": false
            }';
            return $this->response(200, $body);
        }

        if($uri->getPath() === '/disk/test') {
            return $this->response(200, 'file content');
        }
    }
}
