<?php

namespace Clicksign\Http;

use Clicksign\Contracts\ClicksignClientInterface;
use DateTime;

class ClicksignFake implements ClicksignClientInterface
{
    protected array $documents = [];

    protected array $signers = [];

    protected bool $shouldFail = false;

    protected array $responses = [];

    public function shouldFail(bool $fail = true): self
    {
        $this->shouldFail = $fail;

        return $this;
    }

    public function addFakeDocument(string $key, array $data): self
    {
        $this->documents[$key] = array_merge([
            'key' => $key,
            'filename' => 'test-document.pdf',
            'status' => 'running',
            'uploaded_at' => (new DateTime)->format(DateTime::ATOM),
            'updated_at' => (new DateTime)->format(DateTime::ATOM),
            'signers' => [],
            'events' => [],
            'download_url' => "https://fake.url/documents/{$key}/download",
            'metadata' => [],
        ], $data);

        return $this;
    }

    public function addFakeSigner(string $documentKey, string $signerKey, array $data): self
    {
        $this->signers[$documentKey][$signerKey] = array_merge([
            'key' => $signerKey,
            'email' => 'test@example.com',
            'name' => 'Test User',
            'status' => 'pending',
            'created_at' => (new DateTime)->format(DateTime::ATOM),
            'events' => [],
            'metadata' => [],
        ], $data);

        return $this;
    }

    public function setResponse(string $method, string $endpoint, array $response): self
    {
        $this->responses[strtoupper($method)][$endpoint] = $response;

        return $this;
    }

    public function createDocument(array $data): array
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\ClicksignException('Fake error');
        }

        $key = 'fake-doc-'.uniqid();
        $document = [
            'key' => $key,
            'filename' => $data['filename'] ?? 'document.pdf',
            'status' => 'running',
            'uploaded_at' => (new DateTime)->format(DateTime::ATOM),
            'updated_at' => (new DateTime)->format(DateTime::ATOM),
            'signers' => [],
            'events' => [],
            'download_url' => "https://fake.url/documents/{$key}/download",
            'metadata' => $data['metadata'] ?? [],
        ];

        $this->documents[$key] = $document;

        return $document;
    }

    public function getDocument(string $key): array
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($key);
        }

        if (! isset($this->documents[$key])) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($key);
        }

        return $this->documents[$key];
    }

    public function listDocuments(array $filters = []): array
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\ClicksignException('Fake error');
        }

        return array_values($this->documents);
    }

    public function addSigner(string $documentKey, array $signerData): array
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\ClicksignException('Fake error');
        }

        if (! isset($this->documents[$documentKey])) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($documentKey);
        }

        $signerKey = 'fake-signer-'.uniqid();
        $signer = array_merge([
            'key' => $signerKey,
            'status' => 'pending',
            'created_at' => (new DateTime)->format(DateTime::ATOM),
            'events' => [],
            'metadata' => [],
        ], $signerData);

        $this->signers[$documentKey][$signerKey] = $signer;

        return $signer;
    }

    public function removeSigner(string $documentKey, string $signerKey): array
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\ClicksignException('Fake error');
        }

        if (! isset($this->documents[$documentKey])) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($documentKey);
        }

        unset($this->signers[$documentKey][$signerKey]);

        return ['message' => 'Signer removed successfully'];
    }

    public function getDownloadUrl(string $documentKey): string
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($documentKey);
        }

        if (! isset($this->documents[$documentKey])) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($documentKey);
        }

        return "https://fake.url/documents/{$documentKey}/download";
    }

    public function cancelDocument(string $documentKey): array
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\ClicksignException('Fake error');
        }

        if (! isset($this->documents[$documentKey])) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($documentKey);
        }

        $this->documents[$documentKey]['status'] = 'cancelled';

        return $this->documents[$documentKey];
    }

    public function resendNotification(string $documentKey, array $signerKeys = []): array
    {
        if ($this->shouldFail) {
            throw new \Clicksign\Exceptions\ClicksignException('Fake error');
        }

        if (! isset($this->documents[$documentKey])) {
            throw new \Clicksign\Exceptions\DocumentNotFoundException($documentKey);
        }

        return ['message' => 'Notifications sent successfully'];
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function getSigners(string $documentKey): array
    {
        return $this->signers[$documentKey] ?? [];
    }

    public function reset(): self
    {
        $this->documents = [];
        $this->signers = [];
        $this->shouldFail = false;
        $this->responses = [];

        return $this;
    }
}
