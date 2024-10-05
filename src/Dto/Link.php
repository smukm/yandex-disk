<?php

namespace smukm\YandexDisk\Dto;

use smukm\YandexDisk\Api\Api;
use smukm\YandexDisk\Helpers\ResponseCode;

class Link
{
    /**
     * @var string
     */
    public $href;
    /**
     * @var string
     */
    public $method;
    /**
     * @var bool
     */
    public $templated;
    /**
     * @var int
     */
    public $status;
    /**
     * @var string
     */
    public $operation_id;

    public function __construct(
        string $href,
        string $method,
        bool   $templated,
        int $status,
        string $operation_id
    )
    {
        $this->href = $href;
        $this->method = $method;
        $this->templated = $templated;
        $this->status = $status;
        $this->operation_id = $operation_id;
    }

    public static function createFromInfo(array $info, int $statusCode = ResponseCode::HTTP_OK): self
    {
        return new self(
            $info['href'],
            $info['method'],
            $info['templated'],
            $statusCode,
            self::getOperation($info['href'])
        );
    }

    private static function getOperation(string $link): string
    {
        $pos = strpos($link, Api::BASE_URL . '/operations/');
        if($pos === 0) {
            return mb_substr($link, mb_strlen(Api::BASE_URL . '/operations/'));
        }
        return '';
    }
}
