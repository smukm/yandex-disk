<?php

namespace smukm\YandexDisk\Dto;

class ResourceList
{
    /**
     * @var string
     */
    public $sort;
    /**
     * @var string
     */
    public $public_key;
    /**
     * @var array
     */
    public $items;
    /**
     * @var int
     */
    public $limit;
    /**
     * @var int
     */
    public $offset;
    /**
     * @var string
     */
    public $path;
    /**
     * @var int
     */
    public $total;

    public function __construct(
        string $sort,
        string $public_key,
        array  $items,
        int    $limit,
        int    $offset,
        string $path,
        int    $total
    )
    {
        $this->sort = $sort;
        $this->public_key = $public_key;
        $this->items = $items;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->path = $path;
        $this->total = $total;
    }

    public static function createFromInfo(array $info): self
    {
        return new self(
            $info['_embedded']['sort'] ?? '',
            $info['_embedded']['sort'] ?? '',
            $info['_embedded']['items'] ?? [],
            $info['_embedded']['limit'] ?? 0,
            $info['_embedded']['offset'] ?? 0,
            $info['_embedded']['path'] ?? '',
            $info['_embedded']['total'] ?? 0
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
