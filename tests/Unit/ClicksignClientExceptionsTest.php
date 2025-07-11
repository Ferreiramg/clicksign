<?php

use Clicksign\Exceptions\AuthenticationException;
use Clicksign\Exceptions\ClicksignException;
use Clicksign\Exceptions\DocumentNotFoundException;
use Clicksign\Exceptions\ValidationException;
use Clicksign\Http\ClicksignClient;

describe('ClicksignClient Exception Handling', function () {
    beforeEach(function () {
        $this->client = new ClicksignClient('test-token', 'https://api.test.com');
    });

    it('creates client instance', function () {
        expect($this->client)->toBeInstanceOf(ClicksignClient::class);
    });

    it('client has correct constructor', function () {
        expect($this->client)->toBeInstanceOf(ClicksignClient::class);

        // Test that we can access protected properties via reflection
        $reflection = new ReflectionClass($this->client);
        $accessTokenProperty = $reflection->getProperty('accessToken');
        $accessTokenProperty->setAccessible(true);
        $baseUrlProperty = $reflection->getProperty('baseUrl');
        $baseUrlProperty->setAccessible(true);

        expect($accessTokenProperty->getValue($this->client))->toBe('test-token');
        expect($baseUrlProperty->getValue($this->client))->toBe('https://api.test.com');
    });

    it('handles error response method', function () {
        $reflection = new ReflectionClass($this->client);
        $method = $reflection->getMethod('handleErrorResponse');
        $method->setAccessible(true);

        // Mock a response object
        $response = new class
        {
            private int $status;

            private array $data;

            public function __construct(int $status = 200, array $data = [])
            {
                $this->status = $status;
                $this->data = $data;
            }

            public function successful(): bool
            {
                return $this->status >= 200 && $this->status < 300;
            }

            public function status(): int
            {
                return $this->status;
            }

            public function json(): array
            {
                return $this->data;
            }
        };

        // Test successful response (should not throw)
        $successResponse = new $response(200, ['key' => 'doc123']);
        $method->invoke($this->client, $successResponse);
        expect(true)->toBeTrue(); // If we get here, no exception was thrown

        // Test 401 response
        $authResponse = new $response(401, ['message' => 'Unauthorized']);
        expect(fn () => $method->invoke($this->client, $authResponse))
            ->toThrow(AuthenticationException::class, 'Unauthorized');

        // Test 404 response
        $notFoundResponse = new $response(404, ['message' => 'Not found']);
        expect(fn () => $method->invoke($this->client, $notFoundResponse))
            ->toThrow(DocumentNotFoundException::class, 'Not found');

        // Test 422 response with errors in JSON API format
        $validationResponse = new $response(422, [
            'errors' => [
                [
                    'detail' => 'Validation failed',
                    'source' => ['pointer' => '/data/attributes/email'],
                    'title' => 'Invalid email'
                ]
            ]
        ]);

        try {
            $method->invoke($this->client, $validationResponse);
        } catch (ValidationException $e) {
            expect($e->getMessage())->toBe('Validation failed');
            expect($e->getErrors())->toHaveCount(1);
        }

        // Test 500 response
        $serverErrorResponse = new $response(500, ['message' => 'Server error']);
        expect(fn () => $method->invoke($this->client, $serverErrorResponse))
            ->toThrow(ClicksignException::class, 'Server error');

        // Test response with error field instead of message
        $errorResponse = new $response(400, ['error' => 'Bad request']);
        expect(fn () => $method->invoke($this->client, $errorResponse))
            ->toThrow(ClicksignException::class, 'Bad request');

        // Test response with no message or error
        $unknownResponse = new $response(400, ['something' => 'else']);
        expect(fn () => $method->invoke($this->client, $unknownResponse))
            ->toThrow(ClicksignException::class, 'Unknown error occurred');
    });
});
