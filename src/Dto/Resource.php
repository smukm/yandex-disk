<?php

namespace smukm\YandexDisk\Dto;

class Resource
{
    /**
     * @var string
     */
    public $public_key;
    /**
     * @var string
     */
    public $public_url;
    /**
     * @var ResourceList|null
     */
    public $_embedded;
    /**
     * @var string
     */
    public $preview;
    /**
     * @var string
     */
    public $name;
    /**
     * @var array
     */
    public $custom_properties;
    /**
     * @var string
     */
    public $created;
    /**
     * @var string
     */
    public $modified;
    /**
     * @var string
     */
    public $path;
    /**
     * @var string
     */
    public $origin_path;
    /**
     * @var string
     */
    public $md5;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $mime_type;
    /**
     * @var int
     */
    public $size;

    public function __construct(
        string            $public_key,
        string            $public_url,
        ResourceList $_embedded,
        string            $preview,
        string            $name,
        array             $custom_properties,
        string            $created,
        string            $modified,
        string            $path,
        string            $origin_path,
        string            $md5,
        string            $type,
        string            $mime_type,
        int               $size
    )
    {
        $this->public_key = $public_key;
        $this->public_url = $public_url;
        $this->_embedded = $_embedded;
        $this->preview = $preview;
        $this->name = $name;
        $this->custom_properties = $custom_properties;
        $this->created = $created;
        $this->modified = $modified;
        $this->path = $path;
        $this->origin_path = $origin_path;
        $this->md5 = $md5;
        $this->type = $type;
        $this->mime_type = $mime_type;
        $this->size = $size;
    }

    public static function createFromInfo(array $info): self
    {
        return new self(
            $info['public_key'] ?? '',
            $info['public_url'] ?? '',
            ResourceList::createFromInfo($info),
            $info['preview'] ?? '',
            $info['name'] ?? '',
            $info['custom_properties'] ?? [],
            $info['created'] ?? '',
            $info['modified'] ?? '',
            $info['path'] ?? '',
            $info['origin_path'] ?? '',
            $info['md5'] ?? '',
            $info['type'] ?? '',
            $info['mime_type'] ?? '',
            $info['size'] ?? 0
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