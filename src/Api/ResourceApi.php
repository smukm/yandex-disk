<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Dto\Disk;
use smukm\YandexDisk\Dto\FilesResourceList;
use smukm\YandexDisk\Dto\LastUploadedResourceList;
use smukm\YandexDisk\Dto\Resource;
use smukm\YandexDisk\Dto\ResourceList;
use smukm\YandexDisk\Exceptions\YandexDiskException;

final class ResourceApi extends Api
{
    /**
     * Данные о Диске пользователя
     * @return Disk
     * @throws YandexDiskException
     */
    public function getDiskInfo(): Disk
    {
        $response = $this->lib->send(Api::BASE_URL);
        $info = $this->lib->jsonDecodeBodyContents($response);

        return Disk::createFromInfo($info);
    }

    /**
     * Метаинформация о файле или папке
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
            url:Api::BASE_URL . '/resources',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Resource::createFromInfo($info);
    }

    /**
     * Список ресурсов директории
     * @param string $directory
     * @param bool $recursive
     * @return array
     * @throws YandexDiskException
     */
    public function listContents(string $directory, bool $recursive): array
    {
        $ret = [];

        $resource = $this->getMeta($directory);

        if(!($resource->type === 'dir')) {
            return $ret;
        }

        if(!is_iterable($resource->_embedded->items)) {
            return $ret;
        }

        foreach ($resource->_embedded->items as $item) {
            $attr = [
                'type'       => $item['type'],
                'path' => str_replace('disk:/', '',$item['path']),
                'timestamp'  => strtotime($item['modified']),
                'visibility' => (isset($item['public_key'])) ? 'public' : 'private'
            ];

            if($item['type'] === 'file') {
                $attr['md5'] = $item['md5'];
                $attr['mime_type'] = $item['mime_type'];
                $attr['size'] = $item['size'];
            }

            $ret[] = $attr;

            if($recursive && ($item['type'] === 'dir')) {
                if(str_starts_with($item['path'], 'disk:')) {
                    $item['path'] = substr($item['path'], 5);
                }

                $ret = array_merge($ret, $this->listContents($item['path'], true) );
            }
        }

        return $ret;
    }

    /**
     * Добавление метаинформации для ресурса
     * @param string $path
     * @param array $custom_properties
     * @param array{fields: array} $options
     * @return ResourceList
     * @throws YandexDiskException
     */
    public function addMeta(
        string $path,
        array $custom_properties,
        array $options = []
    ): ResourceList
    {
        $options['path'] = $path;

        $allowed_options = [
            'path',
            'fields',
        ];

        $response = $this->lib->send(url: Api::BASE_URL . '/resources',
            query: $this->lib->makeQuery($options, $allowed_options),
            method: 'PATCH',
            body: json_encode(['custom_properties' => $custom_properties])
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return ResourceList::createFromInfo($info);
    }

    /**
     * Последние загруженные файлы
     * @param array{limit: int, media_type: string, fields: array, preview_crop: bool, preview_size: string} $options
     * @return LastUploadedResourceList
     * @throws YandexDiskException
     */
    public function getLastUploaded(array $options = []): LastUploadedResourceList
    {
        $allowed_options = [
            'limit',
            'media_type',
            'fields',
            'preview_crop',
            'preview_size',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/last-uploaded',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return LastUploadedResourceList::createFromInfo($info);
    }

    /**
     * Плоский список всех файлов
     * @param array{limit: int, media_type: string, offset: int, fields: array, preview_crop: bool, preview_size: string} $options
     * @return FilesResourceList
     * @throws YandexDiskException
     */
    public function getFlatList(array $options = []): FilesResourceList
    {
        $allowed_options = [
            'limit',
            'media_type',
            'offset',
            'fields',
            'preview_crop',
            'preview_size',
            'sort',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/files',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return FilesResourceList::createFromInfo($info);
    }
}
