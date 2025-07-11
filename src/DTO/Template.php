<?php

namespace Clicksign\DTO;

class Template
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $content = null,
        public readonly ?string $contentBase64 = null,
        public readonly ?string $color = null,
        public readonly ?string $version = null,
        public readonly ?string $status = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
        public readonly ?array $data = [],
        public readonly ?array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? [];

        return new self(
            id: $data['id'] ?? null,
            name: $attributes['name'] ?? null,
            description: $attributes['description'] ?? null,
            content: $attributes['content'] ?? null,
            contentBase64: $attributes['content_base64'] ?? null,
            color: $attributes['color'] ?? null,
            version: $attributes['version'] ?? null,
            status: $attributes['status'] ?? null,
            createdAt: $attributes['created_at'] ?? null,
            updatedAt: $attributes['updated_at'] ?? null,
            data: $attributes['data'] ?? [],
            metadata: $attributes['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'type' => 'templates',
            'attributes' => array_filter([
                'name' => $this->name,
                'description' => $this->description,
                'content' => $this->content,
                'content_base64' => $this->contentBase64,
                'color' => $this->color,
                'version' => $this->version,
                'status' => $this->status,
                'data' => $this->data,
                'metadata' => $this->metadata,
            ], fn ($value) => $value !== null),
        ], fn ($value) => $value !== null && $value !== []);
    }

    /**
     * Check if template has a required field
     */
    public function hasRequiredField(string $field): bool
    {
        $requiredFields = $this->metadata['required_fields'] ?? [];

        return in_array($field, $requiredFields);
    }

    /**
     * Get variables from template content
     */
    public function getVariables(): array
    {
        if (! $this->content) {
            return [];
        }

        preg_match_all('/\{\{([^}]+)\}\}/', $this->content, $matches);

        return $matches[1] ?? [];
    }

    /**
     * Check if template is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
