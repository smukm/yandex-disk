<?php

namespace smukm\YandexDisk\Tests\responses\resources;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class DeleteNonEmptyDirOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        // upload request
        if($method === 'DELETE') {
            $body = '{
              "href": "https://cloud-api.yandex.net/v1/disk/operations?id=d80c269ce4eb16c0207f0a15t4a31415313452f9e950cd9576f36b1146ee0e42",
              "method": "GET",
              "templated": false
            }';
            return $this->response(202, $body);
        }

        // check status
        if($method === 'GET') {
            $body = '{
              "status":"success"
            }';
            return $this->response(200, $body);
        }

    }
}
