<?php

namespace smukm\YandexDisk\Dto;

class Error
{
    /**
     * @var string
     */
    public $message;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $error;
    /**
     * @var int
     */
    public $code;

    public function __construct(
        string $message,
        string $description,
        string $error,
        int $code
    )
    {
        $this->message = $message;
        $this->description = $description;
        $this->error = $error;
        $this->code = $code;
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
