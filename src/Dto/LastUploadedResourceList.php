<?php

namespace smukm\YandexDisk\Dto;

class LastUploadedResourceList
{
    /**
     * @var array
     */
    public $items;
    /**
     * @var int
     */
    public $limit;
    public function __construct(
        array $items,
        int   $limit
    )
    {
        $this->items = $items;
        $this->limit = $limit;
    }

    public static function createFromInfo(array $info): self
    {
        return new static(
            $info['items'],
            $info['limit']
        );
    }
}
