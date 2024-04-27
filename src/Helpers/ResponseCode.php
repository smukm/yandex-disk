<?php

namespace smukm\YandexDisk\Helpers;

class ResponseCode
{
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NO_CONTENT = 204;


    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_ACCESS_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_NON_ACCEPTABLE = 406;
    public const HTTP_CONFLICT = 409;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_CONTENT_TOO_LARGE = 413;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_LOCKED = 423;
    public const HTTP_TOO_MANY_REQUESTS = 429;

    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_INSUFFICIENT_STORAGE = 507;
}
