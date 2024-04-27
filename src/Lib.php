<?php


use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use smukm\YandexDisk\Exceptions\YandexDiskException;
use smukm\YandexDisk\Helpers\RequestHelper;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class Lib
{
    public function __construct(
        private readonly string          $token,
        private readonly ClientInterface $client
    )
    {
    }

    /**
     * @throws YandexDiskException
     */
    public function send(
        string $url,
        array $query = [],
        string $method = 'GET',
        array $headers = [],
               $body = null
    ): ResponseInterface
    {
        try {
            $request = new Request($method,  $url,
                array_merge([
                    'Accept' => 'application/json',
                    'Authorization' => $this->token
                ], $headers),
                $body
            );

            $options = [];
            if(count($query)) {
                $options = [
                    'query' => $query
                ];
            }

            return $this->client->send($request, $options);

        } catch (BadResponseException  $ex) {

            $err = $this->jsonDecodeBodyContents($ex->getResponse());

            throw YandexDiskException::make(
                $err['message'] ?? $ex->getMessage(),
                $err['description'] ?? $ex->getMessage(),
                $err['error'] ?? $ex->getCode(),
                $ex->getCode()
            );
        } catch (ConnectException $ex) {
            $err = $ex->getHandlerContext();
            throw new YandexDiskException($err['error'],  'Connect problem', 'ConnectException', $ex->getCode());
        } catch (Throwable $ex) {
            throw new YandexDiskException($ex->getMessage(), '', '', $ex->getCode());
        }
    }

    public function jsonDecodeBodyContents(ResponseInterface $response): array
    {
        $body = $response->getBody();
        $ret = json_decode($body->getContents(), true);

        return (is_array($ret)) ? $ret : [];
    }

    public function makeQuery(array $args, array $allowed): array
    {
        $requestHelper = new RequestHelper();
        return $requestHelper->makeQuery($args, $allowed);
    }
}
