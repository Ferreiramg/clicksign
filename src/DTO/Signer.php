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
        public readonly ?string $documentation = null,
        public readonly ?bool $refusable = null,
        public readonly ?int $group = null,
        public readonly ?array $communicateEvents = null,
        public readonly ?string $status = null,
        public readonly ?string $signedAt = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null
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
            documentation: $attributes['documentation'] ?? null,
            refusable: $attributes['refusable'] ?? true,
            group: $attributes['group'] ?? '1',
            communicateEvents: $attributes['communicate_events'] ?? null,
            status: $attributes['status'] ?? null,
            signedAt: $attributes['signed_at'] ?? null,
            createdAt: $attributes['created_at'] ?? null,
            updatedAt: $attributes['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        $return = [
            'type' => 'signers',
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone_number' => $this->phoneNumber,
                'has_documentation' => $this->hasDocumentation,
                'refusable' => $this->refusable,
                'communicate_events' => $this->communicateEvents,
            ],
        ];

        if ($this->group) {
            $return['attributes']['group'] = $this->group;
        }

        if ($this->hasDocumentation) {
            $return['attributes']['documentation'] = $this->documentation;
            $return['attributes']['birthday'] = $this->birthday;
        }

        return $return;
    }

    /**
     * Create a basic signer
     */
    public static function create(
        string $name,
        string $email,
        ?string $birthday = null,
        ?string $phoneNumber = null,
        ?string $documentation = null,
        ?bool $refusable = true,
        ?bool $hasDocumentation = null,
        ?array $communicateEvents = null
    ): self {
        return new self(
            name: $name,
            email: $email,
            birthday: $birthday,
            phoneNumber: $phoneNumber,
            documentation: $documentation,
            refusable: $refusable,
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
