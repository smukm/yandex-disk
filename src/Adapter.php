<?php


use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\PathPrefixer;
use League\Flysystem\UnableToCheckDirectoryExistence;
use League\Flysystem\UnableToCheckFileExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use smukm\YandexDisk\Api\YandexDiskApi;
use smukm\YandexDisk\Dto\Error;
use smukm\YandexDisk\Helpers\ResponseCode;

class Adapter implements FilesystemAdapter
{

    public const PREFIX_FULL = 'disk:/';

    private PathPrefixer $prefixer;

    public function __construct(
        protected YandexDiskApi $client,
                                $prefix = self::PREFIX_FULL
    )
    {
        $this->prefixer = new PathPrefixer($prefix);
    }

    /**
     * @param $path
     * @param $contents
     * @param Config $config
     * @return void
     * @throws FilesystemException
     */
    public function write($path, $contents, Config $config): void
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        try {
            $this->ensureCreatedDir($prefixedPath, $config);

            $this->client->io->uploadFile($prefixedPath, $contents, true);

        } catch (YandexDiskNotFound|YandexDiskConflict) {
        } catch (YandexDiskException $ex) {
            throw UnableToWriteFile::atLocation($prefixedPath, $ex->getError()->message, $ex);
        } catch (Throwable $ex) {
            throw UnableToWriteFile::atLocation($prefixedPath, $ex->getMessage(), $ex);
        }

        $visibility = $config->get('visibility', Visibility::PRIVATE);
        $this->setVisibility($path, $visibility);
    }

    public function writeStream($path, $contents, Config $config): void
    {
        if (!is_resource($contents)) {
            throw new InvalidArgumentException('Must be a resource');
        }

        $prefixedPath = $this->prefixer->prefixPath($path);

        try {
            $this->ensureCreatedDir($prefixedPath, $config);
            $this->client->io->uploadStream($prefixedPath, $contents, true);
        } catch (YandexDiskNotFound|YandexDiskConflict) {
        } catch (YandexDiskException $ex) {
            throw UnableToWriteFile::atLocation($prefixedPath, $ex->getError()->message, $ex);
        } catch (Throwable $ex) {
            throw UnableToWriteFile::atLocation($prefixedPath, $ex->getMessage(), $ex);
        }

        $visibility = $config->get('visibility', Visibility::PRIVATE);
        $this->setVisibility($path, $visibility);
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $prefixedSource = $this->prefixer->prefixPath($source);
        $prefixedDestination = $this->prefixer->prefixPath($destination);

        try {
            $visibility = $this->visibility($source)->visibility();
            $this->client->disk->move(
                $prefixedSource,
                $prefixedDestination,
                true
            );

            $this->setVisibility($destination, $visibility);

        } catch (Throwable $ex) {
            throw UnableToMoveFile::fromLocationTo($prefixedSource, $prefixedDestination, $ex);
        }
    }

    public function copy($source, $destination, $config): void
    {
        $prefixedSource = $this->prefixer->prefixPath($source);
        $prefixedDestination = $this->prefixer->prefixPath($destination);

        try {
            $visibility = $this->visibility($source)->visibility();

            $this->client->disk->copy($prefixedSource, $prefixedDestination, true);

            $this->setVisibility($destination, $visibility);
        } catch (Throwable $ex) {
            throw UnableToCopyFile::fromLocationTo($prefixedSource, $prefixedDestination, $ex);
        }
    }

    public function delete($path): void
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        try {
            $this->client->disk->removeResource($prefixedPath);
        } catch (YandexDiskNotFound) {
        } catch (YandexDiskException $ex) {
            throw UnableToDeleteFile::atLocation($prefixedPath, $ex->getError()->message, $ex);
        } catch (Throwable $ex) {
            throw UnableToDeleteFile::atLocation($prefixedPath, $ex->getMessage(), $ex);
        }
    }

    public function deleteDirectory(string $path): void
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        try {
            $this->client->disk->removeResource($prefixedPath);
        } catch (YandexDiskNotFound) {
        } catch (YandexDiskException $ex) {
            throw UnableToDeleteDirectory::atLocation($prefixedPath, $ex->getError()->message, $ex);
        } catch (Throwable $ex) {
            throw UnableToDeleteDirectory::atLocation($prefixedPath, $ex->getMessage(), $ex);
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        $prefixedPath = trim($prefixedPath, '/');
        $subdirs = explode('/', $prefixedPath);
        $to_create = '/';

        foreach ($subdirs as $subdir) {
            $to_create .= $subdir;
            try {
                $this->client->disk->createDir($to_create);
            } catch (YandexDiskNotFound|YandexDiskConflict) {
            } catch (YandexDiskException $ex) {
                throw UnableToCreateDirectory::atLocation($prefixedPath, $ex->getError()->message, $ex);
            } catch (Throwable $ex) {
                throw UnableToCreateDirectory::atLocation($prefixedPath, $ex->getMessage());
            }
            $to_create .= '/';
        }
    }

    public function setVisibility($path, $visibility): void
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        try {
            if($visibility === Visibility::PUBLIC) {
                $this->client->public->publish($prefixedPath);
            } else {
                $this->client->public->unpublish($prefixedPath);
            }
        } catch (Throwable $ex) {
            throw UnableToSetVisibility::atLocation($prefixedPath, $ex->getMessage(), $ex);
        }
    }

    public function fileExists(string $path): bool
    {
        $prefixedPath = $this->prefixer->prefixPath($path);
        $resource = null;
        try {
            $resource = $this->client->resource->getMeta($prefixedPath);
        } catch (YandexDiskNotFound) {
        } catch (Throwable $ex) {
            throw UnableToCheckFileExistence::forLocation($path, $ex);
        }

        return ($resource && $resource->type === 'file');
    }

    public function directoryExists(string $path): bool
    {
        $prefixedPath = $this->prefixer->prefixPath($path);
        $resource = null;

        try {
            $resource = $this->client->resource->getMeta($prefixedPath);
        } catch (YandexDiskNotFound) {
        } catch (Throwable $ex) {
            throw UnableToCheckDirectoryExistence::forLocation($path, $ex);
        }

        return ($resource && $resource->type === 'dir');
    }

    public function read($path): string
    {
        $prefixedPath = $this->prefixer->prefixPath($path);
        try {
            return $this->client->io->downloadFile($prefixedPath);
        } catch (Throwable $ex) {
            throw UnableToReadFile::fromLocation($prefixedPath, $ex->getMessage(), $ex);
        }
    }

    public function readStream($path)
    {
        $prefixedPath = $this->prefixer->prefixPath($path);
        try {
            $resp = $this->client->io->downloadStream($prefixedPath);
            return $resp->detach();
        } catch (Throwable $ex) {
            throw UnableToReadFile::fromLocation($prefixedPath, $ex->getMessage(), $ex);
        }
    }

    public function listContents($path = '', $deep = false): iterable
    {
        $prefixedPath = $this->prefixer->prefixPath($path);

        $list = $this->client->resource->listContents($prefixedPath, $deep);

        foreach($list as $item) {
            yield $this->getListItem($item);
        }
    }

    private function getListItem($item): DirectoryAttributes|FileAttributes
    {
        if($item['type'] === 'dir') {
            return new DirectoryAttributes(
                $this->prefixer->stripDirectoryPrefix($item['path']),
                $item['visibility'],
                $item['timestamp']
            );
        }
        return new FileAttributes(
            $this->prefixer->stripDirectoryPrefix($item['path']),
            $item['size'],
            $item['visibility'],
            $item['timestamp'],
            $item['mime_type']
        );
    }

    private function getMetadata($path): FileAttributes | DirectoryAttributes | Error
    {
        $prefixedPath = $this->prefixer->prefixPath($path);
        $resource = $this->client->resource->getMeta($prefixedPath);

        if($resource->type === 'dir') {
            return new DirectoryAttributes(
                $prefixedPath,
                (empty($resource->public_key)) ? Visibility::PRIVATE : Visibility::PUBLIC,
                strtotime($resource->modified)
            );
        } else {
            return new FileAttributes(
                $prefixedPath,
                $resource->size,
                (empty($resource->public_key)) ? Visibility::PRIVATE : Visibility::PUBLIC,
                strtotime($resource->modified),
                $resource->mime_type
            );
        }
    }

    public function fileSize(string $path): FileAttributes
    {
        try {
            $resource = $this->getMetadata($path);
            if ($resource instanceof DirectoryAttributes) {
                throw UnableToRetrieveMetadata::create($path, 'file size', $resource->message ?? '');
            }
            return $resource;
        } catch (Throwable $ex) {
            throw UnableToRetrieveMetadata::create($path, 'file size', $resource->message ?? '', $ex);
        }
    }

    public function mimeType(string $path): FileAttributes
    {
        try {
            $resource = $this->getMetadata($path);
            if ($resource instanceof DirectoryAttributes) {
                throw UnableToRetrieveMetadata::create($path, 'mime type', $resource->message ?? '');
            }
            return $resource;
        } catch (Throwable $ex) {
            throw UnableToRetrieveMetadata::create($path, 'mime type', $resource->message ?? '', $ex);
        }
    }

    public function lastModified(string $path): FileAttributes
    {
        try {
            $resource = $this->getMetadata($path);
            if ($resource instanceof DirectoryAttributes) {
                throw UnableToRetrieveMetadata::create($path, 'last modified', $resource->message ?? '');
            }
            return $resource;
        } catch (Throwable $ex) {
            throw UnableToRetrieveMetadata::create($path, 'last modified', $resource->message ?? '', $ex);
        }
    }

    public function visibility(string $path): FileAttributes
    {
        try {
            $resource = $this->getMetadata($path);
            if ($resource instanceof DirectoryAttributes) {
                throw UnableToRetrieveMetadata::create($path, 'visibility', $resource->message ?? '');
            }
            return $resource;
        } catch (Throwable $ex) {
            throw UnableToRetrieveMetadata::create($path, 'visibility', $resource->message ?? '', $ex);
        }
    }

    /**
     * @throws FilesystemException
     */
    protected function ensureCreatedDir($path, $config): void
    {
        $path = $this->prefixer->stripPrefix($path);
        $pi = pathinfo($path);
        if($pi['dirname'] === '.') {
            return;
        }
        if(!$this->directoryExists($pi['dirname'])) {
            $this->createDirectory($pi['dirname'], $config);
        }
    }
}