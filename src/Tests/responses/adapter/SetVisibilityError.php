<?php

namespace smukm\YandexDisk\Tests\responses\adapter;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class SetVisibilityError extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        if($method === 'PUT' && $uri->getPath() === '/v1/disk/resources/publish') {
            return $this->response(
                406,
                file_get_contents(__DIR__ . '/../_json/common_error.json')
            );
        }
    }
}
