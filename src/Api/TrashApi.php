<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Dto\Link;
use smukm\YandexDisk\Dto\Resource;
use smukm\YandexDisk\Exceptions\YandexDiskException;

final class TrashApi extends Api
{
    /**
     * Emptying Trash
     * @param string $path
     * @return Link
     * @throws YandexDiskException
     */
    public function clear(string $path = ''): Link
    {
        $response = $this->lib->send(
            url:Api::BASE_URL . '/trash/resources',
            query: $this->lib->makeQuery(compact('path'), ['path']),
            method: 'DELETE'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Restoring a file or folder from Trash
     * @param string $path
     * @param array{name: string, overwrite:bool} $options
     * @return Link
     * @throws YandexDiskException
     */
    public function restore(string $path, array $options = []): Link
    {
        $options['path'] = $path;
        $allowed_options = [
            'path',
            'name',
            'overwrite',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/trash/resources/restore',
            query: $this->lib->makeQuery($options, $allowed_options),
            method: 'PUT'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * File and folder meta information
     * @param string $path
     * @param array{path: string, fields: array, limit: int, offset: int, preview_crop:bool, preview_size:string} $options
     * @return Resource
     * @throws YandexDiskException
     */
    public function getMeta(string $path, array $options = []): Resource
    {
        $options['path'] = $path;

        $allowed_options = [
            'path',
            'fields',
            'limit',
            'offset',
            'preview_crop',
            'preview_size',
            'sort'
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/trash/resources',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Resource::createFromInfo($info);
    }
}
