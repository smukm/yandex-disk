<?php

namespace smukm\YandexDisk\Exceptions;

class YandexDiskInsufficientStorage extends YandexDiskException
{
    public function __construct(string $message, string $description, string $error, int $code)
    {
        parent::__construct($message, $description, $error, $code);
    }
}
