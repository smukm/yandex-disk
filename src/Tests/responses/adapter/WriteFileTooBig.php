<?php

namespace smukm\YandexDisk\Tests\responses\adapter;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class WriteFileTooBig extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        // directory exists
        if($method === 'GET' && $uri->getPath() === '/v1/disk/resources') {
            return $this->response(
                200,
                file_get_contents(__DIR__ . '/../_json/directory_exists.json')
            );
        }

        // upload request
        if($method === 'GET' && $uri->getPath() === '/v1/disk/resources/upload') {
            return $this->response(
                200,
                file_get_contents(__DIR__ . '/../_json/file_upload_request_ok.json')
            );
        }

        // upload
        if($method === 'PUT' && $uri->getPath() === '/v1/disk/resources') {
            return $this->response(
                413,
                file_get_contents(__DIR__ . '/../_json/file_upload_request_error.json')
            );
        }
    }
}
