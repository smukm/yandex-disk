<?php

namespace smukm\YandexDisk\Tests\responses\disk;

use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\YandexDiskFakeResponseInterface;
use Psr\Http\Message\RequestInterface;

class MetaDiskResourceWithKeyOK extends AbstractYandexDiskFakeResponse
{
    public function run(RequestInterface $request, array $query): Response
    {
        return $this->response(
            200,
            file_get_contents(__DIR__ . '/../_json/resource_meta_with_key.json')
        );
    }
}
