<?php

namespace Clicksign\DTO;

class Document
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $filename = null,
        public readonly ?string $contentBase64 = null,
        public readonly ?string $status = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?string $finishedAt = null,
        public readonly ?string $downloadUrl = null,
        public readonly ?array $template = null,
        public readonly ?array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? [];

        return new self(
            id: $data['id'] ?? null,
            filename: $attributes['filename'] ?? null,
            contentBase64: $attributes['content_base64'] ?? null,
            status: $attributes['status'] ?? null,
            createdAt: $attributes['created_at'] ?? null,
            updatedAt: $attributes['updated_at'] ?? null,
            finishedAt: $attributes['finished_at'] ?? null,
            downloadUrl: $attributes['download_url'] ?? null,
            template: $attributes['template'] ?? null,
            metadata: $attributes['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        $return = [
            'type' => 'documents',
            'attributes' => [
                'filename' => $this->filename,
                'content_base64' => $this->contentBase64,
            ],
        ];

        if ($this->template !== null) {
            $return['attributes']['template'] = $this->template;
        }

        if (! empty($this->metadata)) {
            $return['attributes']['metadata'] = json_encode($this->metadata);
        }

        return $return;
    }

    /**
     * Create a document from file content
     */
    public static function fromFile(string $filename, string $contentBase64, ?array $metadata = []): self
    {

        return new self(
            filename: $filename,
            contentBase64: $contentBase64,
            metadata: $metadata
        );
    }

    /**
     * Create a document from template
     */
    public static function fromTemplate(string $filename, string $templateId, array $templateData = [], ?array $metadata = null): self
    {
        $template = [
            'key' => $templateId,
            'data' => $templateData,
        ];
        if (! empty($metadata)) {
            $template['metadata'] = json_encode($metadata);
        }

        return new self(
            filename: $filename,
            template: $template
        );
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
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
