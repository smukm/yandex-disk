<?php

namespace smukm\YandexDisk\Tests\unit;

use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use smukm\YandexDisk\Adapter;
use smukm\YandexDisk\Api\YandexDiskApi;
use smukm\YandexDisk\Helpers\ResponseCode;
use smukm\YandexDisk\Tests\ClientFake;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use smukm\YandexDisk\Tests\responses\adapter\CommonClientErrors;
use smukm\YandexDisk\Tests\responses\adapter\CopyFileAsyncOK;
use smukm\YandexDisk\Tests\responses\adapter\CopyFileNotFound;
use smukm\YandexDisk\Tests\responses\adapter\CopyFileSyncOK;
use smukm\YandexDisk\Tests\responses\adapter\CreateDirectoryError;
use smukm\YandexDisk\Tests\responses\adapter\DeleteFileError;
use smukm\YandexDisk\Tests\responses\adapter\DeleteFileNotFound;
use smukm\YandexDisk\Tests\responses\adapter\DeleteFileOK;
use smukm\YandexDisk\Tests\responses\adapter\MoveFileAsyncOK;
use smukm\YandexDisk\Tests\responses\adapter\MoveFileNotFound;
use smukm\YandexDisk\Tests\responses\adapter\MoveFileSyncOK;
use smukm\YandexDisk\Tests\responses\adapter\ReadFileError;
use smukm\YandexDisk\Tests\responses\adapter\ReadFileOK;
use smukm\YandexDisk\Tests\responses\adapter\SetVisibilityError;
use smukm\YandexDisk\Tests\responses\adapter\SetVisibilityOK;
use smukm\YandexDisk\Tests\responses\adapter\WriteFileErrorCreateDir;
use smukm\YandexDisk\Tests\responses\adapter\WriteFileOK;
use smukm\YandexDisk\Tests\responses\adapter\WriteFileTooBig;
use smukm\YandexDisk\Tests\responses\adapter\CreateDirectoryOK;
use PHPUnit\Framework\TestCase;

class AdapterMockTest extends TestCase
{
    protected static $adapter = null;

    protected function adapter(AbstractYandexDiskFakeResponse $response): FilesystemAdapter
    {
        $client = new YandexDiskApi(
            'xxxx',
            new ClientFake($response)
        );

        return new Adapter($client, 'Test');
    }

    /**
     *
     */
    public function testBadDataMessage()
    {
        $adapter = $this->adapter(new CommonClientErrors(ResponseCode::HTTP_BAD_REQUEST));
        $this->expectExceptionMessage('Bad data.');
        $adapter->write('text.txt', 'some text', new Config());
    }

    /**
     *
     */
    public function testAccessForbiddenMessage()
    {
        $adapter = $this->adapter(new CommonClientErrors(ResponseCode::HTTP_ACCESS_FORBIDDEN));
        $this->expectExceptionMessage('Access forbidden.');
        $adapter->write('text.txt', 'some text', new Config());
    }

    /**
     *
     */
    public function testServiceUnavailableMessage()
    {
        $adapter = $this->adapter(new CommonClientErrors(ResponseCode::HTTP_SERVICE_UNAVAILABLE));
        $this->expectExceptionMessage('Service unavailable.');
        $adapter->write('text.txt', 'some text', new Config());
    }

    /**
     *
     */
    public function testWriteOK()
    {
        $adapter = $this->adapter(new WriteFileOK());
        $adapter->write('text.txt', 'some text', new Config());

    }

    /**
     *
     */
    public function testWriteFileTooBig()
    {
        $adapter = $this->adapter(new WriteFileTooBig());
        $this->expectException(UnableToWriteFile::class);
        $adapter->write('text.txt', 'some text', new Config());
    }

    /**
     *
     */
    public function testWriteFileUnableCreateDir()
    {
        $adapter = $this->adapter(new WriteFileErrorCreateDir());
        $this->expectException(UnableToWriteFile::class);
        $adapter->write('text.txt', 'some text', new Config());
    }

    /**
     *
     */
    public function testWriteStreamOK()
    {
        $adapter = $this->adapter(new WriteFileOK());
        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, 'content');
        rewind($stream);
        $adapter->writeStream('text.txt', $stream, new Config());
    }

    /**
     *
     */
    public function testWriteStreamFileTooBig()
    {
        $adapter = $this->adapter(new WriteFileTooBig());

        $stream = fopen('php://temp', 'w+b');
        fwrite($stream, 'content');
        rewind($stream);

        $this->expectException(UnableToWriteFile::class);
        $adapter->writeStream('text.txt', $stream, new Config());
    }

    /**
     *
     */
    public function testMoveFileSyncOK()
    {
        $adapter = $this->adapter(new MoveFileSyncOK());
        $adapter->move('11.txt', '12.txt', new Config());

    }

    /**
     *
     */
    public function testMoveFileAsyncOK()
    {
        $adapter = $this->adapter(new MoveFileAsyncOK());
        $adapter->move('11.txt', '12.txt', new Config());
    }
    /**
     *
     */
    public function testMoveFileNotFound()
    {
        $adapter = $this->adapter(new MoveFileNotFound());
        $this->expectException(UnableToMoveFile::class);
        $adapter->move('11.txt', '12.txt', new Config());
    }


    /**
     *
     */
    public function testCopyFileSyncOK()
    {
        $adapter = $this->adapter(new CopyFileSyncOK());
        $adapter->copy('11.txt', '12.txt', new Config());

    }

    /**
     *
     */
    public function testCopyFileAsyncOK()
    {
        $adapter = $this->adapter(new CopyFileAsyncOK());
        $adapter->copy('11.txt', '12.txt', new Config());
    }

    /**
     *
     */
    public function testCopyFileNotFound()
    {
        $adapter = $this->adapter(new CopyFileNotFound());
        $this->expectException(UnableToCopyFile::class);
        $adapter->copy('11.txt', '12.txt', new Config());
    }

    /**
     *
     */
    public function testDeleteFileOk()
    {
        $adapter = $this->adapter(new DeleteFileOK());
        $adapter->delete('11.txt');
    }

    /**
     *
     */
    public function testDeleteFileNotFound()
    {
        $adapter = $this->adapter(new DeleteFileNotFound());
        $adapter->delete('11.txt');
    }

    /**
     *
     */
    public function testDeleteFileError()
    {
        $adapter = $this->adapter(new DeleteFileError());
        $this->expectException(UnableToDeleteFile::class);
        $adapter->delete('11.txt');
    }

    /**
     *
     */
    public function testDeleteDirectoryOk()
    {
        $adapter = $this->adapter(new DeleteFileOK());
        $adapter->deleteDirectory('11.txt');
    }

    /**
     *
     */
    public function testDeleteDirectoryNotFound()
    {
        $adapter = $this->adapter(new DeleteFileNotFound());
        $adapter->deleteDirectory('11.txt');
    }

    /**
     *
     */
    public function testDeleteDirectoryError()
    {
        $adapter = $this->adapter(new DeleteFileError());
        $this->expectException(UnableToDeleteDirectory::class);
        $adapter->deleteDirectory('11.txt');
    }

    /**
     *
     */
    public function testCreateDirectoryOK()
    {
        $adapter = $this->adapter(new CreateDirectoryOK());
        $adapter->createDirectory('new folder', new Config());
    }

    /**
     *
     */
    public function testCreateDirectoryError()
    {
        $adapter = $this->adapter(new CreateDirectoryError());
        $this->expectException(UnableToCreateDirectory::class);
        $adapter->createDirectory('new folder', new Config());
    }

    /**
     *
     */
    public function testSetVisibilityOK()
    {
        $adapter = $this->adapter(new SetVisibilityOK());
        $adapter->setVisibility('11.txt', Visibility::PUBLIC);
    }

    /**
     *
     */
    public function testSetVisibilityError()
    {
        $adapter = $this->adapter(new SetVisibilityError());
        $this->expectException(UnableToSetVisibility::class);
        $adapter->setVisibility('11.txt', Visibility::PUBLIC);
    }

    /**
     *
     */
    public function testReadFileOK()
    {
        $adapter = $this->adapter(new ReadFileOK());
        $adapter->read('11.txt');
    }

    /**
     *
     */
    public function testReadFileError()
    {
        $adapter = $this->adapter(new ReadFileError());
        $this->expectException(UnableToReadFile::class);
        $adapter->read('11.txt');
    }

    /**
     *
     */
    public function testReadStreamFileOK()
    {
        $adapter = $this->adapter(new ReadFileOK());
        $adapter->readStream('11.txt');
    }

    /**
     *
     */
    public function testReadStreamFileError()
    {
        $adapter = $this->adapter(new ReadFileError());
        $this->expectException(UnableToReadFile::class);
        $adapter->readStream('11.txt');
    }
}