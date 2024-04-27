<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Dto\Link;
use smukm\YandexDisk\Exceptions\YandexDiskException;
use Psr\Http\Message\StreamInterface;

final class UploadDownloadApi extends Api
{
    /**
     * Загрузка файла на Диск
     * @param string $path
     * @param string $contents
     * @param bool $overwrite
     * @param array{overwrite: bool, fields: array} $options
     * @return bool
     * @throws YandexDiskException
     */
    public function uploadFile(
        string $path,
        string $contents,
        bool $overwrite = false,
        array $options = []
    ): bool
    {
        $options['path'] = $path;
        $options['overwrite'] = $overwrite;

        $allowed_options = [
            'path',
            'overwrite',
            'fields'
        ];
        // запрос к диску для загрузки
        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/upload',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        $this->lib->send(
            url: $info['href'],
            method: 'PUT',
            body: $contents
        );

        return true;
    }

    /**
     * Загрузка файла на диск побайтово
     * @param string $path
     * @param $resource
     * @param bool $overwrite
     * @param array{overwrite:bool, fields: array} $options
     * @return bool
     * @throws YandexDiskException
     */
    public function uploadStream(
        string $path,
               $resource,
        bool $overwrite = false,
        array $options = []
    ): bool
    {
        $options['path'] = $path;
        $options['overwrite'] = $overwrite;

        $allowed_options = [
            'path',
            'overwrite',
            'fields'
        ];

        // запрос к диску для загрузки
        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/upload',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        $this->lib->send(
            url: $info['href'],
            method: 'PUT',
            headers: [
                'stream' => true,
            ],
            body: $resource
        );

        return true;
    }

    /**
     * Скачивание файла с Диска
     * @param string $path
     * @param array{fields: array} $options
     * @return string
     * @throws YandexDiskException
     */
    public function downloadFile(
        string $path,
        array $options = []
    ): string
    {
        $options['path'] = $path;

        $allowed_options = [
            'path',
            'fields'
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/download',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        $response = $this->lib->send(
            url: $info['href']
        );

        return $response->getBody()->getContents();
    }

    /**
     * Скачивание файла с Диска побайтово
     * @param string $path
     * @param array{fields: array} $options
     * @return StreamInterface
     */
    public function downloadStream(
        string $path,
        array $options = []
    ): StreamInterface
    {
        $options['path'] = $path;

        $allowed_options = [
            'path',
            'fields'
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/download',
            query: $this->lib->makeQuery($options, $allowed_options)
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        $response = $this->lib->send(
            url: $info['href'],
            headers: ['stream' => true]
        );

        return $response->getBody();
    }

    /**
     * Скачивание файла из интернета на Диск
     * @param string $url
     * @param string $path
     * @param array{disable_redirects: bool, fields: array} $options
     * @return Link
     * @throws YandexDiskException
     */
    public function uploadFileFromUrl(
        string $url,
        string $path,
        array $options = []): Link
    {

        $options['path'] = $path;
        $options['url'] = $url;
        $allowed_options = [
            'url',
            'path',
            'disable_redirects',
            'fields'
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/upload',
            query: $this->lib->makeQuery($options, $allowed_options),
            method: 'POST'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }
}
