<?php

namespace smukm\YandexDisk\Tests;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Response;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ClientFake implements ClientInterface
{

    public function __construct(private AbstractYandexDiskFakeResponse $response)
    {}

    public function send(RequestInterface $request, array $query): ResponseInterface
    {
        $response = $this->response->run($request, $query);
        if($response->getStatusCode() > 399) {
            throw new BadResponseException(
                $response->getReasonPhrase(),
                $request,
                $response
            );
        }

        return $response;
    }

    //@todo
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return new Response(404);
    }
}
