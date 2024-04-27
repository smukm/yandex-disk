<?php

namespace smukm\YandexDisk\Tests\responses\adapter;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class SetVisibilityOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        if($method === 'PUT' && $uri->getPath() === '/v1/disk/resources/publish') {
            return $this->response(
                200,
                file_get_contents(__DIR__ . '/../_json/resource_publish_ok.json')
            );
        }
    }
}
