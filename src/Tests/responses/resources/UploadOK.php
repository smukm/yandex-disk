<?php

namespace smukm\YandexDisk\Tests\responses\resources;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class UploadOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();
        if($method === 'GET') {
            $body = '{
              "href": "https://uploader1d.dst.yandex.net:443/upload-target/test",
              "method": "PUT",
              "templated": false
            }';

            return $this->response(200, $body);
        }

        if($method === 'PUT') {
            return $this->response(201);
        }
    }
}
