<?php

namespace smukm\YandexDisk\Dto;

use smukm\YandexDisk\Api\Api;
use smukm\YandexDisk\Helpers\ResponseCode;

class Link
{
    public function __construct(
        public readonly string $href,
        public readonly string $method,
        public readonly bool   $templated,
        public readonly int $status,
        public readonly string $operation_id
    )
    {}

    public static function createFromInfo(array $info, int $statusCode = ResponseCode::HTTP_OK): static
    {
        return new static(
            href: $info['href'],
            method: $info['method'],
            templated: $info['templated'],
            status: $statusCode,
            operation_id: self::getOperation($info['href'])
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
