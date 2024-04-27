<?php

namespace smukm\YandexDisk\Tests\responses\adapter;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class CreateDirectoryOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        if($method === 'PUT' && $uri->getPath() === '/v1/disk/resources') {
            return $this->response(
                201,
                file_get_contents(__DIR__ . '/../_json/create_directory_ok.json')
            );
        }
    }
}
