<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Lib;
use Psr\Http\Client\ClientInterface;

class YandexDiskApi
{
    /**
     * @var ResourceApi
     */
    public $resource;
    /**
     * @var UploadDownloadApi
     */
    public $io;
    /**
     * @var DiskOperationApi
     */
    public $disk;
    /**
     * @var TrashApi
     */
    public $trash;

    /**
     * @var PublicResourceApi
     */
    public $public;

    public function __construct(
        string $token,
        ClientInterface $client
    )
    {
        $lib = new Lib($token, $client);
        $this->resource = new ResourceApi($lib);
        $this->io = new UploadDownloadApi($lib);
        $this->disk = new DiskOperationApi($lib);
        $this->trash = new TrashApi($lib);
        $this->public = new PublicResourceApi($lib);
    }
}
