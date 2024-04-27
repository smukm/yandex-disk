<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Lib;
use Psr\Http\Client\ClientInterface;

class YandexDiskApi
{
    public ResourceApi $resource;
    public UploadDownloadApi $io;
    public DiskOperationApi $disk;
    public TrashApi $trash;
    public PublicResourceApi $public;

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
