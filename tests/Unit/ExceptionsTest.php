<?php

use Clicksign\Exceptions\AuthenticationException;
use Clicksign\Exceptions\ClicksignException;
use Clicksign\Exceptions\DocumentNotFoundException;
use Clicksign\Exceptions\ValidationException;

describe('ClicksignException', function () {
    it('can be instantiated', function () {
        $exception = new ClicksignException('Test message');

        expect($exception)->toBeInstanceOf(ClicksignException::class);
        expect($exception->getMessage())->toBe('Test message');
        expect($exception->getCode())->toBe(0);
    });

    it('can be instantiated with code', function () {
        $exception = new ClicksignException('Test message', 500);

        expect($exception->getMessage())->toBe('Test message');
        expect($exception->getCode())->toBe(500);
    });

    it('extends Exception', function () {
        $exception = new ClicksignException();

        expect($exception)->toBeInstanceOf(\Exception::class);
    });
});

describe('AuthenticationException', function () {
    it('can be instantiated with default message', function () {
        $exception = new AuthenticationException();

        expect($exception)->toBeInstanceOf(AuthenticationException::class);
        expect($exception)->toBeInstanceOf(ClicksignException::class);
        expect($exception->getMessage())->toBe('Authentication failed');
        expect($exception->getCode())->toBe(401);
    });

    it('can be instantiated with custom message', function () {
        $exception = new AuthenticationException('Invalid API token');

        expect($exception->getMessage())->toBe('Invalid API token');
        expect($exception->getCode())->toBe(401);
    });

    it('has correct HTTP status code', function () {
        $exception = new AuthenticationException();

        expect($exception->getCode())->toBe(401);
    });
});

describe('DocumentNotFoundException', function () {
    it('can be instantiated with document key', function () {
        $documentKey = 'abc123';
        $exception = new DocumentNotFoundException($documentKey);

        expect($exception)->toBeInstanceOf(DocumentNotFoundException::class);
        expect($exception)->toBeInstanceOf(ClicksignException::class);
        expect($exception->getMessage())->toBe("Document with key 'abc123' not found");
        expect($exception->getCode())->toBe(404);
    });

    it('formats message correctly with different document keys', function () {
        $exception1 = new DocumentNotFoundException('doc-123-456');
        $exception2 = new DocumentNotFoundException('');

        expect($exception1->getMessage())->toBe("Document with key 'doc-123-456' not found");
        expect($exception2->getMessage())->toBe("Document with key '' not found");
    });

    it('has correct HTTP status code', function () {
        $exception = new DocumentNotFoundException('test');

        expect($exception->getCode())->toBe(404);
    });
});

describe('ValidationException', function () {
    it('can be instantiated with message only', function () {
        $exception = new ValidationException('Validation failed');

        expect($exception)->toBeInstanceOf(ValidationException::class);
        expect($exception)->toBeInstanceOf(ClicksignException::class);
        expect($exception->getMessage())->toBe('Validation failed');
        expect($exception->getCode())->toBe(422);
        expect($exception->getErrors())->toBe([]);
    });

    it('can be instantiated with message and errors', function () {
        $errors = [
            'email' => ['The email field is required.'],
            'name' => ['The name field is required.']
        ];
        $exception = new ValidationException('Validation failed', $errors);

        expect($exception->getMessage())->toBe('Validation failed');
        expect($exception->getCode())->toBe(422);
        expect($exception->getErrors())->toBe($errors);
    });

    it('can get errors', function () {
        $errors = ['field1' => ['error1', 'error2']];
        $exception = new ValidationException('Test', $errors);

        expect($exception->getErrors())->toBe($errors);
    });

    it('returns empty array when no errors provided', function () {
        $exception = new ValidationException('Test message');

        expect($exception->getErrors())->toBe([]);
        expect($exception->getErrors())->toBeArray();
        expect($exception->getErrors())->toHaveCount(0);
    });

    it('has correct HTTP status code', function () {
        $exception = new ValidationException('Test');

        expect($exception->getCode())->toBe(422);
    });

    it('can handle complex error structures', function () {
        $errors = [
            'user.name' => ['Name is required'],
            'user.email' => ['Email is invalid', 'Email is required'],
            'documents' => ['At least one document is required'],
            'signers.0.email' => ['First signer email is invalid']
        ];

        $exception = new ValidationException('Complex validation failed', $errors);

        expect($exception->getErrors())->toBe($errors);
        expect($exception->getErrors())->toHaveKey('user.name');
        expect($exception->getErrors())->toHaveKey('signers.0.email');
    });
});
