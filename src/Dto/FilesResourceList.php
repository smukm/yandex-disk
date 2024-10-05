<?php

namespace smukm\YandexDisk\Dto;

class FilesResourceList
{
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

    public function __construct(
        array $items,
        int   $limit,
        int   $offset
    )
    {
        $this->items = $items;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public static function createFromInfo(array $info): self
    {
        return new static(
            $info['items'],
            $info['limit'],
            $info['offset']
        );
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }
}
