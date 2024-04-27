<?php

namespace smukm\YandexDisk\Dto;

class LastUploadedResourceList
{
    public function __construct(
        public readonly array $items,
        public readonly int   $limit
    )
    {
    }

    public static function createFromInfo(array $info): static
    {
        return new static(
            items: $info['items'],
            limit: $info['limit']
        );
    }
}
