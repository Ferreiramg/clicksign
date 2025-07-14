<?php

namespace Clicksign\DTO;

class Envelope
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $locale = null,
        public readonly ?bool $autoClose = null,
        public readonly ?int $remindInterval = null,
        public readonly ?bool $blockAfterRefusal = null,
        public readonly ?string $deadlineAt = null,
        public readonly ?string $status = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?array $documents = [],
        public readonly ?array $signers = [],
        public readonly ?array $requirements = []
    ) {}

    public static function create(
        string $name,
        ?string $locale = null,
        ?bool $autoClose = null,
        ?int $remindInterval = null,
        ?bool $blockAfterRefusal = null,
        ?string $deadlineAt = null
    ): self {
        return new self(
            name: $name,
            locale: $locale,
            autoClose: $autoClose,
            remindInterval: $remindInterval,
            blockAfterRefusal: $blockAfterRefusal,
            deadlineAt: $deadlineAt
        );
    }

    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? [];
        $relationships = $data['relationships'] ?? [];

        return new self(
            id: $data['id'] ?? null,
            name: $attributes['name'] ?? null,
            locale: $attributes['locale'] ?? null,
            autoClose: $attributes['auto_close'] ?? null,
            remindInterval: $attributes['remind_interval'] ?? null,
            blockAfterRefusal: $attributes['block_after_refusal'] ?? null,
            deadlineAt: $attributes['deadline_at'] ?? null,
            status: $attributes['status'] ?? null,
            createdAt: $attributes['created_at'] ?? null,
            updatedAt: $attributes['updated_at'] ?? null,
            documents: $relationships['documents']['data'] ?? [],
            signers: $relationships['signers']['data'] ?? [],
            requirements: $relationships['requirements']['data'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => 'envelopes',
            'attributes' => array_filter([
                'name' => $this->name,
                'locale' => $this->locale,
                'auto_close' => $this->autoClose,
                'remind_interval' => $this->remindInterval,
                'block_after_refusal' => $this->blockAfterRefusal,
                'deadline_at' => $this->deadlineAt
            ], fn ($value) => $value !== null || empty($value)),
        ], fn ($value) => $value !== null && ! empty($value));
    }

    public function toUpdateArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'type' => 'envelopes',
            'attributes' => array_filter([
                'name' => $this->name,
                'locale' => $this->locale,
                'auto_close' => $this->autoClose,
                'remind_interval' => $this->remindInterval,
                'block_after_refusal' => $this->blockAfterRefusal,
                'deadline_at' => $this->deadlineAt,
                'status' => $this->status,
            ], fn ($value) => $value !== null || empty($value)),
        ], fn ($value) => $value !== null && ! empty($value));
    }

    /**
     * Add document to envelope
     */
    public function addDocument(Document $document): self
    {
        $documents = $this->documents;
        $documents[] = $document;

        return new self(
            id: $this->id,
            name: $this->name,
            locale: $this->locale,
            autoClose: $this->autoClose,
            remindInterval: $this->remindInterval,
            blockAfterRefusal: $this->blockAfterRefusal,
            deadlineAt: $this->deadlineAt,
            status: $this->status,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
            documents: $documents,
            signers: $this->signers,
            requirements: $this->requirements
        );
    }

    /**
     * Add signer to envelope
     */
    public function addSigner(Signer $signer): self
    {
        $signers = $this->signers;
        $signers[] = $signer;

        return new self(
            id: $this->id,
            name: $this->name,
            locale: $this->locale,
            autoClose: $this->autoClose,
            remindInterval: $this->remindInterval,
            blockAfterRefusal: $this->blockAfterRefusal,
            deadlineAt: $this->deadlineAt,
            status: $this->status,
            createdAt: $this->createdAt,
            updatedAt: $this->updatedAt,
            documents: $this->documents,
            signers: $signers,
            requirements: $this->requirements
        );
    }

    /**
     * Check if envelope is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if envelope is sent
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if envelope is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if envelope has deadline
     */
    public function hasDeadline(): bool
    {
        return $this->deadlineAt !== null;
    }

    /**
     * Check if envelope has reminders enabled
     */
    public function hasReminders(): bool
    {
        return $this->remindInterval !== null && $this->remindInterval > 0;
    }

    /**
     * Get completion percentage
     */
    public function getProgressPercentage(): float
    {
        if (empty($this->signers)) {
            return 0.0;
        }

        $signedCount = 0;
        foreach ($this->signers as $signer) {
            if (is_array($signer) && ($signer['status'] ?? '') === 'signed') {
                $signedCount++;
            } elseif ($signer instanceof Signer && $signer->hasSigned()) {
                $signedCount++;
            }
        }

        return ($signedCount / count($this->signers)) * 100;
    }
}
