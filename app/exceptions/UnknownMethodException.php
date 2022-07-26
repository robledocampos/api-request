<?php


class UnknownMethodException extends Exception
{
    const INVALID_METHOD = "Method should be: GET, POST, PATCH, PUT or DELETE";

    function __construct($message = self::INVALID_METHOD)
    {
        parent::__construct($message);
    }
}
