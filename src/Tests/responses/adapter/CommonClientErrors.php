<?php

namespace smukm\YandexDisk\Tests\responses\adapter;

use smukm\YandexDisk\Helpers\ResponseCode;
use smukm\YandexDisk\Tests\responses\AbstractYandexDiskFakeResponse;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CommonClientErrors extends AbstractYandexDiskFakeResponse
{

    public function __construct(
        public readonly int $code
    )
    {}

    public function run(RequestInterface $request, array $query): ResponseInterface
    {
        $body = json_decode(file_get_contents(__DIR__ . '/../_json/common_error.json'), true);

        switch ($this->code) {
            case ResponseCode::HTTP_BAD_REQUEST: // Некорректные данные.
                $body['message'] = 'Bad data.';
                break;
            case ResponseCode::HTTP_UNAUTHORIZED: // Не авторизован.
                $body['message'] = 'Non autorized.';
                break;
            case ResponseCode::HTTP_ACCESS_FORBIDDEN: // API недоступно. Ваши файлы занимают больше места, чем у вас есть. Удалите лишнее или увеличьте объём Диска
                $body['message'] = 'Access forbidden.';
                break;
            case ResponseCode::HTTP_NOT_FOUND: // Не удалось найти запрошенный ресурс.
            case ResponseCode::HTTP_NON_ACCEPTABLE: // Ресурс не может быть представлен в запрошенном формате.
            case ResponseCode::HTTP_CONFLICT: // Ресурс уже существует
            case ResponseCode::HTTP_PRECONDITION_FAILED: // Перемещение папки с проектом Wfolio в чужую ОП не поддерживается
            case ResponseCode::HTTP_UNSUPPORTED_MEDIA_TYPE: // Неподдерживаемый формат данных в теле запроса.
            case ResponseCode::HTTP_LOCKED: // Технические работы. Сейчас можно только просматривать и скачивать файлы.
            case ResponseCode::HTTP_TOO_MANY_REQUESTS: // Слишком много запросов.
                $body['message'] = 'Too many requests.';
                break;
            case ResponseCode::HTTP_SERVICE_UNAVAILABLE: // Сервис временно недоступен.
                $body['message'] = 'Service unavailable.';
                break;
            case ResponseCode::HTTP_INSUFFICIENT_STORAGE: // Недостаточно свободного места.
                $body['message'] = 'Недостаточно свободного места.';
                break;

            case ResponseCode::HTTP_CONTENT_TOO_LARGE: // Загрузка файла недоступна. Файл слишком большой.
                $body['message'] = 'Загрузка файла недоступна. Файл слишком большой.';
                $body['reason'] = 'Some reason';
                $body['limit'] = 123456;

        }

        return $this->response(
            $this->code,
            json_encode($body)
        );
    }
}
