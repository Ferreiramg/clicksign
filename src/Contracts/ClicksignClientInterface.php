<?php

namespace Clicksign\Contracts;

interface ClicksignClientInterface
{
    // Envelope operations
    public function createEnvelope(array $data): array;
    public function getEnvelope(string $envelopeId): array;
    public function updateEnvelope(string $envelopeId, array $data): array;
    public function listEnvelopes(array $filters = []): array;

    // Document operations
    public function createDocument(string $envelopeId, array $data): array;
    public function getDocument(string $envelopeId, string $documentId): array;
    public function listDocuments(string $envelopeId): array;

    // Signer operations
    public function createSigner(string $envelopeId, array $data): array;
    public function getSigner(string $envelopeId, string $signerId): array;
    public function listSigners(string $envelopeId): array;
    public function updateSigner(string $envelopeId, string $signerId, array $data): array;
    public function deleteSigner(string $envelopeId, string $signerId): array;

    // Requirement operations
    public function createRequirement(string $envelopeId, array $data): array;
    public function getRequirement(string $envelopeId, string $requirementId): array;
    public function listRequirements(string $envelopeId): array;
    public function deleteRequirement(string $envelopeId, string $requirementId): array;
    public function bulkRequirements(string $envelopeId, array $operations): array;

    // Notification operations
    public function sendNotification(string $envelopeId, array $data = []): array;

    // Template operations
    public function createTemplate(array $data): array;
    public function getTemplate(string $templateId): array;
    public function listTemplates(array $filters = []): array;
    public function updateTemplate(string $templateId, array $data): array;
    public function deleteTemplate(string $templateId): array;

    // Events
    public function getDocumentEvents(string $envelopeId, string $documentId): array;
    public function getEnvelopeEvents(string $envelopeId): array;
}
