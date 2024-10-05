<?php

namespace smukm\YandexDisk\Dto;

class PublicResourcesList
{
    /**
     * @var array
     */
    public $items;
    /**
     * @var string
     */
    public $type;
    /**
     * @var int
     */
    public $limit;
    /**
     * @var int
     */
    public $offset;

    public function __construct(
        array  $items,
        string $type,
        int    $limit,
        int    $offset
    )
    {
        $this->items = $items;
        $this->type = $type;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public static function createFromInfo(array $info): self
    {
        return new self(
            $info['items'] ?? [],
            $info['type'] ?? '',
            $info['limit'] ?? 20,
            $info['offset'] ?? 0
        );
    }
}
