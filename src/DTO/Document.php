<?php

namespace Clicksign\DTO;

class Document
{
    public function __construct(
        public readonly string $key,
        public readonly string $filename,
        public readonly ?string $uploadedAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $finishedAt = null,
        public readonly ?string $status = null,
        public readonly ?array $signers = [],
        public readonly ?array $events = [],
        public readonly ?string $downloadUrl = null,
        public readonly ?array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            key: $data['key'] ?? '',
            filename: $data['filename'] ?? '',
            uploadedAt: $data['uploaded_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            finishedAt: $data['finished_at'] ?? null,
            status: $data['status'] ?? null,
            signers: $data['signers'] ?? [],
            events: $data['events'] ?? [],
            downloadUrl: $data['download_url'] ?? null,
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'filename' => $this->filename,
            'uploaded_at' => $this->uploadedAt,
            'updated_at' => $this->updatedAt,
            'finished_at' => $this->finishedAt,
            'status' => $this->status,
            'signers' => $this->signers,
            'events' => $this->events,
            'download_url' => $this->downloadUrl,
            'metadata' => $this->metadata,
        ];
    }

    public function isCompleted(): bool
    {
        return $this->status === 'closed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isPending(): bool
    {
        return $this->status === 'running';
    }
}
