<?php

namespace smukm\YandexDisk\Dto;

class ResourceList
{
    public function __construct(
        public readonly string $sort,
        public readonly string $public_key,
        public readonly array  $items,
        public readonly int    $limit,
        public readonly int    $offset,
        public readonly string $path,
        public readonly int    $total,
    )
    {
    }

    public static function createFromInfo(array $info): static
    {
        return new static(
            sort: $info['_embedded']['sort'] ?? '',
            public_key: $info['_embedded']['sort'] ?? '',
            items: $info['_embedded']['items'] ?? [],
            limit: $info['_embedded']['limit'] ?? 0,
            offset: $info['_embedded']['offset'] ?? 0,
            path: $info['_embedded']['path'] ?? '',
            total: $info['_embedded']['total'] ?? 0
        );
    }
    public function toArray(): array
    {
        return [
            'sort' => $this->sort,
            'public_key' => $this->public_key,
            'items' => $this->items,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'path' => $this->path,
            'total' => $this->total,
        ];
    }
}
