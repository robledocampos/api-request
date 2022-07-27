<?php


class JsonEncodeException extends Exception
{
    const UTF8_CHARACTER = "Input has non utf-8 character.";

    function __construct($message = self::UTF8_CHARACTER)
    {
        parent::__construct($message);
    }
}
