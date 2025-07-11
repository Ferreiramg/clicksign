<?php

namespace Clicksign\Http;

use Clicksign\Contracts\ClicksignClientInterface;
use DateTime;

class ClicksignFake implements ClicksignClientInterface
{
    protected array $envelopes = [];
    protected array $documents = [];
    protected array $signers = [];
    protected array $requirements = [];
    protected array $templates = [];
    protected bool $shouldFail = false;

    public function shouldFail(bool $fail = true): self
    {
        $this->shouldFail = $fail;
        return $this;
    }

    // Envelope operations
    public function createEnvelope(array $data): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Fake failure');
        }

        $envelopeId = 'envelope_' . uniqid();
        $attributes = $data['attributes'] ?? [];
        
        $this->envelopes[$envelopeId] = [
            'id' => $envelopeId,
            'type' => 'envelopes',
            'attributes' => array_merge([
                'name' => 'Test Envelope',
                'locale' => 'pt-BR',
                'status' => 'draft',
                'auto_close' => true,
                'remind_interval' => 3,
                'block_after_refusal' => true,
                'created_at' => (new DateTime)->format(DateTime::ATOM),
                'updated_at' => (new DateTime)->format(DateTime::ATOM),
            ], $attributes)
        ];

        return [
            'data' => $this->envelopes[$envelopeId]
        ];
    }

    public function getEnvelope(string $envelopeId): array
    {
        if ($this->shouldFail || !isset($this->envelopes[$envelopeId])) {
            throw new \Exception('Envelope not found');
        }

        return [
            'data' => $this->envelopes[$envelopeId]
        ];
    }

    public function updateEnvelope(string $envelopeId, array $data): array
    {
        if ($this->shouldFail || !isset($this->envelopes[$envelopeId])) {
            throw new \Exception('Envelope not found');
        }

        $attributes = $data['attributes'] ?? [];
        $this->envelopes[$envelopeId]['attributes'] = array_merge(
            $this->envelopes[$envelopeId]['attributes'],
            $attributes
        );

        return [
            'data' => $this->envelopes[$envelopeId]
        ];
    }

    public function listEnvelopes(array $filters = []): array
    {
        return [
            'data' => array_values($this->envelopes)
        ];
    }

    // Document operations
    public function createDocument(string $envelopeId, array $data): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Fake failure');
        }

        $documentId = 'document_' . uniqid();
        $attributes = $data['attributes'] ?? [];
        
        $this->documents[$envelopeId][$documentId] = [
            'id' => $documentId,
            'type' => 'documents',
            'attributes' => array_merge([
                'filename' => 'test-document.pdf',
                'status' => 'draft',
                'created_at' => (new DateTime)->format(DateTime::ATOM),
            ], $attributes)
        ];

        return [
            'data' => $this->documents[$envelopeId][$documentId]
        ];
    }

    public function getDocument(string $envelopeId, string $documentId): array
    {
        if ($this->shouldFail || !isset($this->documents[$envelopeId][$documentId])) {
            throw new \Exception('Document not found');
        }

        return [
            'data' => $this->documents[$envelopeId][$documentId]
        ];
    }

    public function listDocuments(string $envelopeId): array
    {
        return [
            'data' => array_values($this->documents[$envelopeId] ?? [])
        ];
    }

    // Signer operations
    public function createSigner(string $envelopeId, array $data): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Fake failure');
        }

        $signerId = 'signer_' . uniqid();
        $attributes = $data['attributes'] ?? [];
        
        $this->signers[$envelopeId][$signerId] = [
            'id' => $signerId,
            'type' => 'signers',
            'attributes' => array_merge([
                'name' => 'Test Signer',
                'email' => 'test@example.com',
                'status' => 'pending',
                'has_documentation' => true,
                'created_at' => (new DateTime)->format(DateTime::ATOM),
            ], $attributes)
        ];

        return [
            'data' => $this->signers[$envelopeId][$signerId]
        ];
    }

    public function getSigner(string $envelopeId, string $signerId): array
    {
        if ($this->shouldFail || !isset($this->signers[$envelopeId][$signerId])) {
            throw new \Exception('Signer not found');
        }

        return [
            'data' => $this->signers[$envelopeId][$signerId]
        ];
    }

    public function listSigners(string $envelopeId): array
    {
        return [
            'data' => array_values($this->signers[$envelopeId] ?? [])
        ];
    }

    public function updateSigner(string $envelopeId, string $signerId, array $data): array
    {
        if ($this->shouldFail || !isset($this->signers[$envelopeId][$signerId])) {
            throw new \Exception('Signer not found');
        }

        $attributes = $data['attributes'] ?? [];
        $this->signers[$envelopeId][$signerId]['attributes'] = array_merge(
            $this->signers[$envelopeId][$signerId]['attributes'],
            $attributes
        );

        return [
            'data' => $this->signers[$envelopeId][$signerId]
        ];
    }

    public function deleteSigner(string $envelopeId, string $signerId): array
    {
        if ($this->shouldFail || !isset($this->signers[$envelopeId][$signerId])) {
            throw new \Exception('Signer not found');
        }

        unset($this->signers[$envelopeId][$signerId]);
        return ['data' => null];
    }

    // Requirement operations
    public function createRequirement(string $envelopeId, array $data): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Fake failure');
        }

        $requirementId = 'requirement_' . uniqid();
        $attributes = $data['attributes'] ?? [];
        
        $this->requirements[$envelopeId][$requirementId] = [
            'id' => $requirementId,
            'type' => 'requirements',
            'attributes' => array_merge([
                'action' => 'agree',
                'role' => 'sign',
                'created_at' => (new DateTime)->format(DateTime::ATOM),
            ], $attributes),
            'relationships' => $data['relationships'] ?? []
        ];

        return [
            'data' => $this->requirements[$envelopeId][$requirementId]
        ];
    }

    public function getRequirement(string $envelopeId, string $requirementId): array
    {
        if ($this->shouldFail || !isset($this->requirements[$envelopeId][$requirementId])) {
            throw new \Exception('Requirement not found');
        }

        return [
            'data' => $this->requirements[$envelopeId][$requirementId]
        ];
    }

    public function listRequirements(string $envelopeId): array
    {
        return [
            'data' => array_values($this->requirements[$envelopeId] ?? [])
        ];
    }

    public function deleteRequirement(string $envelopeId, string $requirementId): array
    {
        if ($this->shouldFail || !isset($this->requirements[$envelopeId][$requirementId])) {
            throw new \Exception('Requirement not found');
        }

        unset($this->requirements[$envelopeId][$requirementId]);
        return ['data' => null];
    }

    public function bulkRequirements(string $envelopeId, array $operations): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Bulk operation failed');
        }

        return [
            'data' => [
                'id' => 'bulk_' . uniqid(),
                'type' => 'bulk_operations',
                'attributes' => [
                    'status' => 'completed',
                    'operations_count' => count($operations['atomic:operations'] ?? [])
                ]
            ]
        ];
    }

    // Notification operations
    public function sendNotification(string $envelopeId, array $data = []): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Notification failed');
        }

        return [
            'data' => [
                'id' => 'notification_' . uniqid(),
                'type' => 'notifications',
                'attributes' => [
                    'status' => 'sent',
                    'sent_at' => (new DateTime)->format(DateTime::ATOM)
                ]
            ]
        ];
    }

    public function sendNotifications(string $envelopeId, array $data): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Fake failure');
        }

        return [
            'data' => [
                'id' => 'notification_' . uniqid(),
                'type' => 'notifications',
                'attributes' => [
                    'message' => $data['message'] ?? 'Notification sent',
                    'sent_at' => now()->toISOString(),
                    'envelope_id' => $envelopeId
                ]
            ]
        ];
    }

    // Template operations
    public function createTemplate(array $data): array
    {
        if ($this->shouldFail) {
            throw new \Exception('Template creation failed');
        }

        $templateId = 'template_' . uniqid();
        $attributes = $data['attributes'] ?? [];
        
        $this->templates[$templateId] = [
            'id' => $templateId,
            'type' => 'templates',
            'attributes' => array_merge([
                'name' => 'Test Template',
                'created_at' => (new DateTime)->format(DateTime::ATOM),
            ], $attributes)
        ];

        return [
            'data' => $this->templates[$templateId]
        ];
    }

    public function getTemplate(string $templateId): array
    {
        if ($this->shouldFail || !isset($this->templates[$templateId])) {
            throw new \Exception('Template not found');
        }

        return [
            'data' => $this->templates[$templateId]
        ];
    }

    public function listTemplates(array $filters = []): array
    {
        return [
            'data' => array_values($this->templates)
        ];
    }

    public function updateTemplate(string $templateId, array $data): array
    {
        if ($this->shouldFail || !isset($this->templates[$templateId])) {
            throw new \Exception('Template not found');
        }

        $attributes = $data['attributes'] ?? [];
        $this->templates[$templateId]['attributes'] = array_merge(
            $this->templates[$templateId]['attributes'],
            $attributes
        );

        return [
            'data' => $this->templates[$templateId]
        ];
    }

    public function deleteTemplate(string $templateId): array
    {
        if ($this->shouldFail || !isset($this->templates[$templateId])) {
            throw new \Exception('Template not found');
        }

        unset($this->templates[$templateId]);
        return ['data' => null];
    }

    // Events
    public function getDocumentEvents(string $envelopeId, string $documentId): array
    {
        return [
            'data' => [
                [
                    'id' => 'event_' . uniqid(),
                    'type' => 'events',
                    'attributes' => [
                        'action' => 'document_created',
                        'created_at' => (new DateTime)->format(DateTime::ATOM)
                    ]
                ]
            ]
        ];
    }

    public function getEnvelopeEvents(string $envelopeId): array
    {
        return [
            'data' => [
                [
                    'id' => 'event_' . uniqid(),
                    'type' => 'events',
                    'attributes' => [
                        'action' => 'envelope_created',
                        'created_at' => (new DateTime)->format(DateTime::ATOM)
                    ]
                ]
            ]
        ];
    }

    // Helper methods for testing
    public function reset(): self
    {
        $this->envelopes = [];
        $this->documents = [];
        $this->signers = [];
        $this->requirements = [];
        $this->templates = [];
        $this->shouldFail = false;

        return $this;
    }
}
