<?php

namespace smukm\YandexDisk\Exceptions;

use RuntimeException;
use smukm\YandexDisk\Dto\Error;
use smukm\YandexDisk\Helpers\ResponseCode;

class YandexDiskException extends RuntimeException
{
    protected Error $error;
    public function __construct(string $message, string $description, string $error, int $code)
    {
        $this->error = new Error(
            message: $message,
            description: $description,
            error: $error,
            code: $code
        );
        parent::__construct($message, $code);
    }

    public function getError(): Error
    {
        return $this->error;
    }

    public static function make(string $message, string $description, string $error, int $code): YandexDiskException
    {
        return match ($code) {
            ResponseCode::HTTP_BAD_REQUEST => new YandexDiskBadRequest($message, $description, $error, $code),
            ResponseCode::HTTP_UNAUTHORIZED => new YandexDiskUnauthorized($message, $description, $error, $code),
            ResponseCode::HTTP_ACCESS_FORBIDDEN => new YandexDiskAccessForbidden($message, $description, $error, $code),
            ResponseCode::HTTP_NOT_FOUND => new YandexDiskNotFound($message, $description, $error, $code),
            ResponseCode::HTTP_NON_ACCEPTABLE => new YandexDiskNotAcceptable($message, $description, $error, $code),
            ResponseCode::HTTP_CONFLICT => new YandexDiskConflict($message, $description, $error, $code),
            ResponseCode::HTTP_PRECONDITION_FAILED => new YandexDiskPreconditionFailed($message, $description, $error, $code),
            ResponseCode::HTTP_UNSUPPORTED_MEDIA_TYPE => new YandexDiskUnsupportedMediaType($message, $description, $error, $code),
            ResponseCode::HTTP_LOCKED => new YandexDiskLocked($message, $description, $error, $code),
            ResponseCode::HTTP_TOO_MANY_REQUESTS => new YandexDiskTooManyRequests($message, $description, $error, $code),
            ResponseCode::HTTP_SERVICE_UNAVAILABLE => new YandexDiskServiceUnavailable($message, $description, $error, $code),
            ResponseCode::HTTP_INSUFFICIENT_STORAGE => new YandexDiskInsufficientStorage($message, $description, $error, $code),
            ResponseCode::HTTP_CONTENT_TOO_LARGE => new YandexDiskContentTooLarge($message, $description, $error, $code),
            default => new self($message, $description, $error, $code)
        };
    }
}
