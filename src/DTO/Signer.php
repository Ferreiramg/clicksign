<?php

namespace Clicksign\DTO;

class Signer
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $birthday = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?bool $hasDocumentation = null,
        public readonly ?bool $refusable = null,
        public readonly ?int $group = null,
        public readonly ?array $communicateEvents = null,
        public readonly ?string $status = null,
        public readonly ?string $signedAt = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? [];

        return new self(
            id: $data['id'] ?? null,
            name: $attributes['name'] ?? null,
            email: $attributes['email'] ?? null,
            birthday: $attributes['birthday'] ?? null,
            phoneNumber: $attributes['phone_number'] ?? null,
            hasDocumentation: $attributes['has_documentation'] ?? null,
            refusable: $attributes['refusable'] ?? null,
            group: $attributes['group'] ?? null,
            communicateEvents: $attributes['communicate_events'] ?? null,
            status: $attributes['status'] ?? null,
            signedAt: $attributes['signed_at'] ?? null,
            createdAt: $attributes['created_at'] ?? null,
            updatedAt: $attributes['updated_at'] ?? null,
            metadata: $attributes['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => 'signers',
            'attributes' => array_filter([
                'name' => $this->name,
                'email' => $this->email,
                'birthday' => $this->birthday,
                'phone_number' => $this->phoneNumber,
                'has_documentation' => $this->hasDocumentation,
                'refusable' => $this->refusable,
                'group' => $this->group,
                'communicate_events' => $this->communicateEvents,
                'metadata' => $this->metadata,
            ], fn ($value) => $value !== null),
        ], fn ($value) => $value !== null && $value !== []);
    }

    /**
     * Create a basic signer
     */
    public static function create(
        string $name,
        string $email,
        ?string $birthday = null,
        ?bool $hasDocumentation = null,
        ?array $communicateEvents = null
    ): self {
        return new self(
            name: $name,
            email: $email,
            birthday: $birthday,
            hasDocumentation: $hasDocumentation,
            communicateEvents: $communicateEvents ?? [
                'document_signed' => 'email',
                'signature_request' => 'email',
                'signature_reminder' => 'email',
            ]
        );
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
