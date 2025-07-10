<?php

namespace Clicksign\Http;

use Clicksign\Contracts\ClicksignClientInterface;
use Clicksign\Exceptions\AuthenticationException;
use Clicksign\Exceptions\ClicksignException;
use Clicksign\Exceptions\DocumentNotFoundException;
use Clicksign\Exceptions\ValidationException;
use Illuminate\Support\Facades\Http;

class ClicksignClient implements ClicksignClientInterface
{
    protected string $baseUrl;

    protected string $accessToken;

    public function __construct(string $accessToken, string $baseUrl = 'https://app.clicksign.com/api/v1')
    {
        $this->accessToken = $accessToken;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function createDocument(array $data): array
    {
        $response = $this->makeRequest('POST', '/documents', $data);

        return $response->json();
    }

    public function getDocument(string $key): array
    {
        $response = $this->makeRequest('GET', "/documents/{$key}");

        return $response->json();
    }

    public function listDocuments(array $filters = []): array
    {
        $response = $this->makeRequest('GET', '/documents', $filters);

        return $response->json();
    }

    public function addSigner(string $documentKey, array $signerData): array
    {
        $response = $this->makeRequest('POST', "/documents/{$documentKey}/list", $signerData);

        return $response->json();
    }

    public function removeSigner(string $documentKey, string $signerKey): array
    {
        $response = $this->makeRequest('DELETE', "/documents/{$documentKey}/list/{$signerKey}");

        return $response->json();
    }

    public function getDownloadUrl(string $documentKey): string
    {
        $response = $this->makeRequest('GET', "/documents/{$documentKey}/download");

        return $response->json('download_url');
    }

    public function cancelDocument(string $documentKey): array
    {
        $response = $this->makeRequest('PATCH', "/documents/{$documentKey}/cancel");

        return $response->json();
    }

    public function resendNotification(string $documentKey, array $signerKeys = []): array
    {
        $data = empty($signerKeys) ? [] : ['signers' => $signerKeys];
        $response = $this->makeRequest('POST', "/documents/{$documentKey}/resend", $data);

        return $response->json();
    }

    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $url = $this->baseUrl.$endpoint;

        $request = Http::withHeaders([
            'Authorization' => $this->accessToken,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        $response = match (strtoupper($method)) {
            'GET' => $request->get($url, $data),
            'POST' => $request->post($url, $data),
            'PUT' => $request->put($url, $data),
            'PATCH' => $request->patch($url, $data),
            'DELETE' => $request->delete($url, $data),
            default => throw new ClicksignException("Unsupported HTTP method: {$method}")
        };

        $this->handleErrorResponse($response);

        return $response;
    }

    protected function handleErrorResponse($response): void
    {
        if ($response->successful()) {
            return;
        }

        $statusCode = $response->status();
        $body = $response->json();
        $message = $body['message'] ?? $body['error'] ?? 'Unknown error occurred';

        match ($statusCode) {
            401 => throw new AuthenticationException($message),
            404 => throw new DocumentNotFoundException($message),
            422 => throw new ValidationException($message, $body['errors'] ?? []),
            default => throw new ClicksignException($message, $statusCode)
        };
    }
}
