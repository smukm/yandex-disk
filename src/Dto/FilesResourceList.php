<?php

namespace smukm\YandexDisk\Dto;

class FilesResourceList
{
    public function __construct(
        public readonly array $items,
        public readonly int   $limit,
        public readonly int   $offset,
    )
    {
    }

    public static function createFromInfo(array $info): static
    {
        return new static(
            items: $info['items'],
            limit: $info['limit'],
            offset: $info['offset']
        );
    }

    public function toArray()
    {
        return [
            'items' => $this->items,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }
}
