<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Dto\Link;
use smukm\YandexDisk\Dto\PublicResourcesList;
use smukm\YandexDisk\Dto\Resource;
use smukm\YandexDisk\Exceptions\YandexDiskException;

final class PublicResourceApi extends Api
{
    /**
     * Публикация ресурса
     * @param string $path
     * @return Link
     * @throws YandexDiskException
     */
    public function publish(string $path): Link
    {
        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/publish',
            query: $this->lib->makeQuery(compact('path'), ['path']),
            method: 'PUT'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Закрытие доступа к ресурсу
     * @param string $path
     * @return Link
     * @throws YandexDiskException
     */
    public function unpublish(string $path): Link
    {
        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/unpublish',
            query: $this->lib->makeQuery(compact('path'), ['path']),
            method: 'PUT'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Метаинформация о публичном ресурсе
     * @param string $public_key
     * @param array{path: string, sort: string, limit: int, offset: int, preview_crop: bool, preview_size: string} $options
     * @return Resource
     * @throws YandexDiskException
     */
    public function getMeta(string $public_key, array $options = []): Resource
    {
        $options['public_key'] = $public_key;
        $allowed_options = [
            'public_key',
            'path',
            'sort',
            'limit',
            'offset',
            'preview_crop',
            'preview_size',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/public/resources',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Resource::createFromInfo($info);
    }

    /**
     * Скачивание публичного файла или папки
     * @param string $public_key
     * @param array{path: string} $options
     * @return Link
     * @throws YandexDiskException
     */
    public function download(string $public_key, array $options = []): Link
    {
        $options['public_key'] = $public_key;
        $allowed_options = [
            'public_key',
            'path',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/public/resources/download',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Сохранение публичного файла в «Загрузки»
     * @param string $public_key
     * @param array{path: string, name: string} $options
     * @return Link
     * @throws YandexDiskException
     */
    public function saveToDisk(string $public_key, array $options = []): Link
    {
        $options['public_key'] = $public_key;
        $allowed_options = [
            'public_key',
            'path',
            'name',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/public/resources/save-to-disk',
            query: $this->lib->makeQuery($options, $allowed_options),
            method: 'POST'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Список опубликованных ресурсов
     * @param array{limit: int, offset: int, type: string, fields: array, preview_size: string} $options
     * @return PublicResourcesList
     * @throws YandexDiskException
     */
    public function list(array $options = []): PublicResourcesList
    {
        $allowed_options = [
            'limit',
            'offset',
            'type',
            'fields',
            'preview_size',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/public',
            query: $this->lib->makeQuery($options, $allowed_options),
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return PublicResourcesList::createFromInfo($info);
    }
}