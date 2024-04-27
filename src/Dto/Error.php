<?php

namespace smukm\YandexDisk\Dto;

class Error
{
    public function __construct(
        public readonly string $message,
        public readonly string $description,
        public readonly string $error,
        public readonly int    $code
    )
    {
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'description' => $this->description,
            'error' => $this->error,
            'code' => $this->code,
        ];
    }
}
