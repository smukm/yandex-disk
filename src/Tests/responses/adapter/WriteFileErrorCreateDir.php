<?php

namespace smukm\YandexDisk\Tests\responses\adapter;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use Psr\Http\Message\RequestInterface;

class WriteFileErrorCreateDir extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        // directory non exists
        if($method === 'GET' && $uri->getPath() === '/v1/disk/resources') {
            return $this->response(
                404,
                file_get_contents(__DIR__ . '/../_json/resource_not_found.json')
            );
        }

        // create dir
        if($method === 'PUT' && $uri->getPath() === '/v1/disk/resources') {
            return $this->response(
                400,
                file_get_contents(__DIR__ . '/../_json/create_directory_error.json')
            );
        }
    }
}
