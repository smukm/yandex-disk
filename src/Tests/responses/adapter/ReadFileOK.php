<?php

namespace smukm\YandexDisk\Tests\responses\adapter;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class ReadFileOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        $uri = $request->getUri();
        $method = $request->getMethod();

        // download request
        if($method === 'GET' && $uri->getPath() === '/v1/disk/resources/download') {
            return $this->response(
                200,
                file_get_contents(__DIR__ . '/../_json/file_download_ok.json')
            );
        }

        //download
        if($method === 'GET') {
            return $this->response(
                200,
                'content of file'
            );
        }
    }
}
