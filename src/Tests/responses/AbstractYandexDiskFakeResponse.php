<?php

namespace smukm\YandexDisk\Tests\responses;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractYandexDiskFakeResponse
{
    abstract public function run(RequestInterface $request, array $query): ResponseInterface;

    public function response(int $status, string $body=''): Response
    {
        $headers = '{
              "cache-control": "no-cache",
              "content-type": "application/json; charset=utf-8"
            }';

        return new Response($status, json_decode($headers, true), $body);
    }
}
