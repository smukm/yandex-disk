<?php

namespace smukm\YandexDisk\Dto;

class Disk
{
    /**
     * @var int
     */
    public $trash_size;
    /**
     * @var int
     */
    public $total_space;
    /**
     * @var int
     */
    public $used_space;
    /**
     * @var array
     */
    public $system_folders;

    public function __construct(
        int $trash_size,
        int $total_space,
        int $used_space,
        array $system_folders
    )
    {
        $this->trash_size = $trash_size;
        $this->total_space = $total_space;
        $this->used_space = $used_space;
        $this->system_folders = $system_folders;
    }

    public static function createFromInfo(array $info): self
    {
        return new static(
            $info['trash_size'],
            $info['total_space'],
            $info['used_space'],
            $info['system_folders']
        );
    }
}
