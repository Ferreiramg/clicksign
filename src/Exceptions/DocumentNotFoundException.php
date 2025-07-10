<?php

namespace Clicksign\Exceptions;

class DocumentNotFoundException extends ClicksignException
{
    public function __construct(string $documentKey)
    {
        parent::__construct("Document with key '{$documentKey}' not found", 404);
    }
}
