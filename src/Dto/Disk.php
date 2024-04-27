<?php

namespace smukm\YandexDisk\Dto;

class Disk
{
    public function __construct(
        public readonly int   $trash_size,
        public readonly int   $total_space,
        public readonly int   $used_space,
        public readonly array $system_folders
    )
    {
    }

    public static function createFromInfo(array $info): static
    {
        return new static(
            trash_size: $info['trash_size'],
            total_space: $info['total_space'],
            used_space: $info['used_space'],
            system_folders: $info['system_folders']
        );
    }
}
