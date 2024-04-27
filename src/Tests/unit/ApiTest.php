<?php

namespace smukm\YandexDisk\Tests\unit;

use Codeception\Test\Unit;
use smukm\YandexDisk\Api\YandexDiskApi;
use smukm\YandexDisk\Dto\Disk;
use smukm\YandexDisk\Dto\Link;
use smukm\YandexDisk\Tests\ClientFake;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\disk\DiskInfo;
use smukm\YandexDisk\Tests\responses\disk\MetaDiskResourceOK;
use smukm\YandexDisk\Tests\responses\disk\MetaDiskResourceWithKeyOK;
use smukm\YandexDisk\Tests\responses\resources\CopyOK;
use smukm\YandexDisk\Tests\responses\resources\CreateOK;
use smukm\YandexDisk\Tests\responses\resources\DeleteNonEmptyDirOK;
use smukm\YandexDisk\Tests\responses\resources\DeleteOK;
use smukm\YandexDisk\Tests\responses\resources\DownloadOK;
use smukm\YandexDisk\Tests\responses\resources\MoveOK;
use smukm\YandexDisk\Tests\responses\resources\UploadOK;
use smukm\YandexDisk\Tests\responses\trash_resources\MetaTrashResourceOK;

class ApiTest extends Unit
{
    protected $tester;


    protected function client(AbstractYandexDiskFakeResponse $response): YandexDiskApi
    {
        return new YandexDiskApi(
            'xxxx',
            new ClientFake($response)
        );
    }

    public function testDiskInfo()
    {
        $client = $this->client(new DiskInfo());
        $disk = $client->resource->getDiskInfo();
        $this->assertInstanceOf(Disk::class, $disk);
        $this->assertEquals(319975063552, $disk->total_space);
        $this->assertEquals(4631577437, $disk->trash_size);
        $this->assertEquals(26157681270, $disk->used_space);
        $this->assertCount(2, $disk->system_folders);
    }

    public function testMetaDiskInfo()
    {
        $client = $this->client(new MetaDiskResourceOK());
        $info = $client->resource
            ->getMeta('path')
            ->toArray();

        $this->assertIsArray($info);
        $this->assertEquals('disk:/foo', $info['_embedded']['path']);
        $this->assertCount(2, $info['_embedded']['items']);
        $this->assertEquals('disk:/foo/bar', $info['_embedded']['items'][0]['path']);
        $this->assertEquals('dir', $info['_embedded']['items'][0]['type']);
        $this->assertEquals('disk:/foo/photo.png', $info['_embedded']['items'][1]['path']);
        $this->assertEquals('file', $info['_embedded']['items'][1]['type']);

        $this->assertEquals(20, $info['_embedded']['limit']);
        $this->assertEquals(0, $info['_embedded']['offset']);

        $this->assertEquals('foo', $info['name']);
        $this->assertEquals('2014-04-21T14:54:42+04:00', $info['created']);
        $this->assertIsArray($info['custom_properties']);
        $this->assertEquals('1', $info['custom_properties']['foo']);
        $this->assertEquals('https://yadi.sk/d/AaaBbb1122Ccc', $info['public_url']);
    }

    public function testMetaTrashInfo()
    {
        $client = $this->client(new MetaTrashResourceOK());
        $info = $client->trash
            ->getMeta('path')
            ->toArray();

        $this->assertIsArray($info);
        $this->assertEquals('https://downloader.disk.yandex.ru/preview/...', $info['preview']);
        $this->assertEquals('cat.png', $info['name']);
        $this->assertEquals('2014-07-16T13:07:45+04:00', $info['created']);
        $this->assertEquals('1', $info['custom_properties']['foo']);
        $this->assertEquals('disk:/foo/cat.png', $info['origin_path']);
        $this->assertEquals('trash:/cat.png', $info['path']);
        $this->assertEquals('image/png', $info['mimetype']);
        $this->assertEquals(903337, $info['size']);
    }

    public function testMetaInfoWithKeys()
    {
        $client = $this->client(new MetaDiskResourceWithKeyOK());
        $info = $client->resource
            ->getMeta('path', ['fields' =>['name','_embedded.items.path']])
            ->toArray();

        $this->assertIsArray($info);
        $this->assertCount(2, $info['_embedded']['items']);
        $this->assertEquals('foo', $info['name']);

    }

    public function testUpload()
    {
        $client = $this->client(new UploadOK());
        $result = $client->io->uploadFile('path', 'contents');

        $this->assertTrue($result);
    }

    public function testDownload()
    {
        $client = $this->client(new DownloadOK());
        $result = $client->io->downloadFile('test_path');

        $this->assertEquals('file content', $result);
    }

    public function testCopy()
    {
        $client = $this->client(new CopyOK());
        $result = $client->disk->copy('from/path', 'to/path');
        $this->assertInstanceOf(Link::class, $result);
    }

    public function testRename()
    {
        $client = $this->client(new MoveOK());
        $result = $client->disk->move('from/path', 'to/path');

        $this->assertInstanceOf(Link::class, $result);
    }

    public function testDelete()
    {
        $client = $this->client(new DeleteOK());
        $result = $client->disk->removeResource('path');

        $this->assertTrue($result);
    }

    public function testDeleteNonEmptyDirectory()
    {
        $client = $this->client(new DeleteNonEmptyDirOK());
        $result = $client->disk->removeResource('path');
        $this->assertInstanceOf(Link::class, $result);

        // check status
        $result = $client->disk->getStatus('xxxx');
        $this->assertEquals('success', $result['status']);
    }

    public function testCreate()
    {
        $client = $this->client(new CreateOK());
        $result = $client->disk->createDir('path');
        $this->assertInstanceOf(Link::class, $result);
    }
}
