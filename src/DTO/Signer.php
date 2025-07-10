<?php

namespace Clicksign\DTO;

class Signer
{
    public function __construct(
        public readonly string $key,
        public readonly string $email,
        public readonly string $name,
        public readonly ?string $documentationNumber = null,
        public readonly ?string $birthday = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?bool $hasDocumentation = null,
        public readonly ?string $deliveryMethod = null,
        public readonly ?string $authenticationMethod = null,
        public readonly ?string $status = null,
        public readonly ?string $signedAt = null,
        public readonly ?string $createdAt = null,
        public readonly ?array $events = [],
        public readonly ?array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'] ?? '',
            email: $data['email'] ?? '',
            name: $data['name'] ?? '',
            documentationNumber: $data['documentation'] ?? null,
            birthday: $data['birthday'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            hasDocumentation: $data['has_documentation'] ?? null,
            deliveryMethod: $data['delivery'] ?? null,
            authenticationMethod: $data['authentication'] ?? null,
            status: $data['status'] ?? null,
            signedAt: $data['signed_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            events: $data['events'] ?? [],
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'key' => $this->key,
            'email' => $this->email,
            'name' => $this->name,
            'documentation' => $this->documentationNumber,
            'birthday' => $this->birthday,
            'phone_number' => $this->phoneNumber,
            'has_documentation' => $this->hasDocumentation,
            'delivery' => $this->deliveryMethod,
            'authentication' => $this->authenticationMethod,
            'status' => $this->status,
            'signed_at' => $this->signedAt,
            'created_at' => $this->createdAt,
            'events' => $this->events,
            'metadata' => $this->metadata,
        ], fn ($value) => $value !== null);
    }

    public function hasSigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isViewing(): bool
    {
        return $this->status === 'viewing';
    }
}
