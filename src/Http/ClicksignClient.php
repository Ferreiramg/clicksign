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

    public function __construct(string $accessToken, string $baseUrl = 'https://app.clicksign.com/api/v3')
    {
        $this->accessToken = $accessToken;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    // Envelope operations
    public function createEnvelope(array $data): array
    {
        $response = $this->makeRequest('POST', '/envelopes', ['data' => $data]);
        return $response->json();
    }

    public function getEnvelope(string $envelopeId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}");
        return $response->json();
    }

    public function updateEnvelope(string $envelopeId, array $data): array
    {
        $response = $this->makeRequest('PATCH', "/envelopes/{$envelopeId}", ['data' => $data]);
        return $response->json();
    }

    public function listEnvelopes(array $filters = []): array
    {
        $response = $this->makeRequest('GET', '/envelopes', $filters);
        return $response->json();
    }

    // Document operations
    public function createDocument(string $envelopeId, array $data): array
    {
        $response = $this->makeRequest('POST', "/envelopes/{$envelopeId}/documents", ['data' => $data]);
        return $response->json();
    }

    public function getDocument(string $envelopeId, string $documentId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/documents/{$documentId}");
        return $response->json();
    }

    public function listDocuments(string $envelopeId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/documents");
        return $response->json();
    }

    // Signer operations
    public function createSigner(string $envelopeId, array $data): array
    {
        $response = $this->makeRequest('POST', "/envelopes/{$envelopeId}/signers", ['data' => $data]);
        return $response->json();
    }

    public function getSigner(string $envelopeId, string $signerId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/signers/{$signerId}");
        return $response->json();
    }

    public function listSigners(string $envelopeId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/signers");
        return $response->json();
    }

    public function updateSigner(string $envelopeId, string $signerId, array $data): array
    {
        $response = $this->makeRequest('PATCH', "/envelopes/{$envelopeId}/signers/{$signerId}", ['data' => $data]);
        return $response->json();
    }

    public function deleteSigner(string $envelopeId, string $signerId): array
    {
        $response = $this->makeRequest('DELETE', "/envelopes/{$envelopeId}/signers/{$signerId}");
        return $response->json();
    }

    // Requirement operations
    public function createRequirement(string $envelopeId, array $data): array
    {
        $response = $this->makeRequest('POST', "/envelopes/{$envelopeId}/requirements", ['data' => $data]);
        return $response->json();
    }

    public function getRequirement(string $envelopeId, string $requirementId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/requirements/{$requirementId}");
        return $response->json();
    }

    public function listRequirements(string $envelopeId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/requirements");
        return $response->json();
    }

    public function deleteRequirement(string $envelopeId, string $requirementId): array
    {
        $response = $this->makeRequest('DELETE', "/envelopes/{$envelopeId}/requirements/{$requirementId}");
        return $response->json();
    }

    public function bulkRequirements(string $envelopeId, array $operations): array
    {
        $response = $this->makeRequest('POST', "/envelopes/{$envelopeId}/bulk_requirements", $operations);
        return $response->json();
    }

    // Notification operations
    public function sendNotification(string $envelopeId, array $data = []): array
    {
        $requestData = ['data' => array_merge([
            'type' => 'notifications',
            'attributes' => []
        ], $data)];
        
        $response = $this->makeRequest('POST', "/envelopes/{$envelopeId}/notifications", $requestData);
        return $response->json();
    }

    // Template operations
    public function createTemplate(array $data): array
    {
        $response = $this->makeRequest('POST', '/templates', ['data' => $data]);
        return $response->json();
    }

    public function getTemplate(string $templateId): array
    {
        $response = $this->makeRequest('GET', "/templates/{$templateId}");
        return $response->json();
    }

    public function listTemplates(array $filters = []): array
    {
        $response = $this->makeRequest('GET', '/templates', $filters);
        return $response->json();
    }

    public function updateTemplate(string $templateId, array $data): array
    {
        $response = $this->makeRequest('PATCH', "/templates/{$templateId}", ['data' => $data]);
        return $response->json();
    }

    public function deleteTemplate(string $templateId): array
    {
        $response = $this->makeRequest('DELETE', "/templates/{$templateId}");
        return $response->json();
    }

    // Events
    public function getDocumentEvents(string $envelopeId, string $documentId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/documents/{$documentId}/events");
        return $response->json();
    }

    public function getEnvelopeEvents(string $envelopeId): array
    {
        $response = $this->makeRequest('GET', "/envelopes/{$envelopeId}/events");
        return $response->json();
    }

    protected function makeRequest(string $method, string $endpoint, array $data = [])
    {
        $url = $this->baseUrl . $endpoint;

        $request = Http::withHeaders([
            'Authorization' => $this->accessToken,
            'Content-Type' => 'application/vnd.api+json',
            'Accept' => 'application/vnd.api+json',
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
        
        // Handle JSON API error format
        $message = 'Unknown error occurred';
        $errors = [];
        
        if (isset($body['errors']) && is_array($body['errors'])) {
            $firstError = $body['errors'][0] ?? [];
            $message = $firstError['detail'] ?? $firstError['title'] ?? $message;
            $errors = $body['errors'];
        } elseif (isset($body['message'])) {
            $message = $body['message'];
        } elseif (isset($body['error'])) {
            $message = $body['error'];
        }

        match ($statusCode) {
            401 => throw new AuthenticationException($message),
            404 => throw new DocumentNotFoundException($message),
            422 => throw new ValidationException($message, $errors),
            default => throw new ClicksignException($message, $statusCode)
        };
    }
}
