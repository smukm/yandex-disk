<?php

namespace smukm\YandexDisk\Dto;

class PublicResourcesList
{
    public function __construct(
        public readonly array  $items,
        public readonly string $type,
        public readonly int    $limit,
        public readonly int    $offset
    )
    {
    }

    public static function createFromInfo(array $info): static
    {
        return new static(
            items: $info['items'] ?? [],
            type: $info['type'] ?? '',
            limit: $info['limit'] ?? 20,
            offset: $info['offset'] ?? 0
        );
    }
}
