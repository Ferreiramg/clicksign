<?php

namespace Clicksign\DTO;

class Requirement
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $action = null,
        public readonly ?string $role = null,
        public readonly ?string $auth = null,
        public readonly ?string $status = null,
        public readonly ?string $documentId = null,
        public readonly ?string $signerId = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null
    ) {}

    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new self(
            id: $data['id'] ?? null,
            action: $attributes['action'] ?? null,
            role: $attributes['role'] ?? null,
            auth: $attributes['auth'] ?? null,
            status: $attributes['status'] ?? null,
            documentId: $relationships['document']['data']['id'] ?? null,
            signerId: $relationships['signer']['data']['id'] ?? null,
            createdAt: $attributes['created_at'] ?? null,
            updatedAt: $attributes['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        $data = [
            'type' => 'requirements',
            'attributes' => array_filter([
                'action' => $this->action,
                'role' => $this->role,
                'auth' => $this->auth,
                'status' => $this->status,
            ], fn ($value) => $value !== null),
            'relationships' => [],
        ];

        if ($this->documentId) {
            $data['relationships']['document'] = [
                'data' => ['type' => 'documents', 'id' => $this->documentId],
            ];
        }

        if ($this->signerId) {
            $data['relationships']['signer'] = [
                'data' => ['type' => 'signers', 'id' => $this->signerId],
            ];
        }

        if (empty($data['relationships'])) {
            unset($data['relationships']);
        }

        return $data;
    }

    /**
     * Create a signature requirement
     */
    public static function createSignatureRequirement(string $documentId, string $signerId, string $role = 'sign'): self
    {
        return new self(
            action: 'agree',
            role: $role,
            documentId: $documentId,
            signerId: $signerId
        );
    }

    /**
     * Create an authentication requirement
     */
    public static function createAuthRequirement(string $documentId, string $signerId, string $auth = 'email'): self
    {
        return new self(
            action: 'provide_evidence',
            documentId: $documentId,
            auth: $auth,
            signerId: $signerId
        );
    }

    /**
     * Check if this is a signature requirement
     */
    public function isSignatureRequirement(): bool
    {
        return $this->action === 'agree' || $this->role === 'sign';
    }

    /**
     * Check if this is an authentication requirement
     */
    public function isAuthRequirement(): bool
    {
        return $this->action === 'provide_evidence' || $this->action === 'approve';
    }

    /**
     * Check if requirement is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if requirement is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' || $this->status === null;
    }
}
