<?php

namespace smukm\YandexDisk\Api;

use smukm\YandexDisk\Dto\Link;
use smukm\YandexDisk\Helpers\ResponseCode;

final class DiskOperationApi extends Api
{
    /**
     * Creating a folder
     * @param string $path
     * @param array $fields
     * @return Link
     */
    public function createDir(string $path, array $fields = []): Link
    {
        $allowed_options = [
            'path',
            'fields',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources',
            query: $this->lib->makeQuery(compact(
                'path', 'fields'
            ), $allowed_options),
            method: 'PUT'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Deleting a file or folder
     * @param string $path
     * @param bool $permanently
     * @param array $fields
     * @return Link|bool
     */
    public function removeResource(
        string $path,
        bool $permanently = false,
        array $fields = []
    ): Link|bool
    {
        $allowed_options = [
            'path',
            'permanently',
            'fields',
        ];

        $response = $this->lib->send(url:Api::BASE_URL . '/resources',
            query: $this->lib->makeQuery(compact(
                'path', 'permanently', 'fields'
            ), $allowed_options),
            method: 'DELETE'
        );

        if($response->getStatusCode() === ResponseCode::HTTP_NO_CONTENT) {
            return true;
        }

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Copying a file or folder
     * @param string $from
     * @param string $path
     * @param bool $overwrite
     * @param array $fields
     * @return Link
     */
    public function copy(
        string $from,
        string $path,
        bool $overwrite = false,
        array $fields = []
    ): Link
    {
        $allowed_options = [
            'from',
            'path',
            'overwrite',
            'fields',
        ];

        $response = $this->lib->send(url:Api::BASE_URL . '/resources/copy',
            query: $this->lib->makeQuery(compact(
                'from', 'path', 'overwrite', 'fields'
            ), $allowed_options),
            method: 'POST'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info);
    }

    /**
     * Moving a file or folder
     * @param string $from
     * @param string $path
     * @param bool $overwrite
     * @param array $fields
     * @return Link
     */
    public function move(
        string $from,
        string $path,
        bool $overwrite = false,
        array $fields = []

    ): Link
    {
        $allowed_options = [
            'from',
            'path',
            'overwrite',
            'fields',
        ];

        $response = $this->lib->send(
            url:Api::BASE_URL . '/resources/move',
            query: $this->lib->makeQuery(compact(
                'from', 'path', 'overwrite', 'fields'
            ), $allowed_options),
            method: 'POST'
        );

        $info = $this->lib->jsonDecodeBodyContents($response);

        return Link::createFromInfo($info, $response->getStatusCode());
    }

    /**
     * Operation status
     * @param $operation_id
     * @return array
     */
    public function getStatus($operation_id): array
    {
        $response = $this->lib->send(
            Api::BASE_URL . "/operations/$operation_id"
        );

        return $this->lib->jsonDecodeBodyContents($response);
    }
}