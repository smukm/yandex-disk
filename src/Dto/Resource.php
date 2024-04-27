<?php

namespace smukm\YandexDisk\Dto;

class Resource
{
    public function __construct(
        public readonly string            $public_key,
        public readonly string            $public_url,
        public readonly ResourceList|null $_embedded,
        public readonly string            $preview,
        public readonly string            $name,
        public readonly array             $custom_properties,
        public readonly string            $created,
        public readonly string            $modified,
        public readonly string            $path,
        public readonly string            $origin_path,
        public readonly string            $md5,
        public readonly string            $type,
        public readonly string            $mime_type,
        public readonly int               $size
    )
    {}

    public static function createFromInfo(array $info): static
    {
        return new static(
            public_key: $info['public_key'] ?? '',
            public_url: $info['public_url'] ?? '',
            _embedded: ResourceList::createFromInfo($info),
            preview: $info['preview'] ?? '',
            name: $info['name'] ?? '',
            custom_properties: $info['custom_properties'] ?? [],
            created: $info['created'] ?? '',
            modified: $info['modified'] ?? '',
            path: $info['path'] ?? '',
            origin_path: $info['origin_path'] ?? '',
            md5: $info['md5'] ?? '',
            type: $info['type'] ?? '',
            mime_type: $info['mime_type'] ?? '',
            size: $info['size'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'public_key' => $this->public_key,
            'public_url' => $this->public_url,
            '_embedded' => $this->_embedded->toArray(),
            'custom_properties' => $this->custom_properties,
            'preview' => $this->preview,
            'name' => $this->name,
            'created' => $this->created,
            'modified' => $this->modified,
            'timestamp' => strtotime($this->modified),
            'path' => $this->path,
            'origin_path' => $this->origin_path,
            'md5' => $this->md5,
            'type' => $this->type,
            'mimetype' => $this->mime_type,
            'size' => $this->size,
        ];
    }
}