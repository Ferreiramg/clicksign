<?php

namespace Clicksign\Exceptions;

class AuthenticationException extends ClicksignException
{
    public function __construct(string $message = 'Authentication failed')
    {
        parent::__construct($message, 401);
    }
}
