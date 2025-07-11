<?php

namespace Clicksign\Support;

use Clicksign\Contracts\ClicksignClientInterface;
use Clicksign\DTO\Document;
use Clicksign\DTO\Envelope;
use Clicksign\DTO\Requirement;
use Clicksign\DTO\Signer;

class ClicksignWorkflow
{
    protected ClicksignClientInterface $client;

    public function __construct(ClicksignClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Create a complete signature workflow following the basic flow
     */
    public function createSignatureWorkflow(
        string $envelopeName,
        string $filename,
        string $contentBase64,
        array $signers,
        array $envelopeOptions = [],
        array $requirementOptions = []
    ): array {
        // Step 1: Create envelope
        $envelope = new Envelope(
            name: $envelopeName,
            locale: $envelopeOptions['locale'] ?? 'pt-BR',
            autoClose: $envelopeOptions['auto_close'] ?? true,
            remindInterval: $envelopeOptions['remind_interval'] ?? 3,
            blockAfterRefusal: $envelopeOptions['block_after_refusal'] ?? true,
            deadlineAt: $envelopeOptions['deadline_at'] ?? null
        );

        $envelopeResponse = $this->client->createEnvelope($envelope->toArray());
        $envelopeId = $envelopeResponse['data']['id'];

        // Step 2: Create document
        $document = Document::fromFile($filename, $contentBase64);
        $documentResponse = $this->client->createDocument($envelopeId, $document->toArray());
        $documentId = $documentResponse['data']['id'];

        $results = [
            'envelope' => $envelopeResponse,
            'document' => $documentResponse,
            'signers' => [],
            'requirements' => []
        ];

        // Step 3: Create signers and requirements
        foreach ($signers as $signerData) {
            // Create signer
            $signer = Signer::create(
                name: $signerData['name'],
                email: $signerData['email'],
                birthday: $signerData['birthday'] ?? null,
                hasDocumentation: $signerData['has_documentation'] ?? true,
                communicateEvents: $signerData['communicate_events'] ?? null
            );

            $signerResponse = $this->client->createSigner($envelopeId, $signer->toArray());
            $signerId = $signerResponse['data']['id'];
            $results['signers'][] = $signerResponse;

            // Create signature requirement
            $signatureRequirement = Requirement::createSignatureRequirement(
                documentId: $documentId,
                signerId: $signerId,
                type: $requirementOptions['type'] ?? 'click'
            );

            $signatureReqResponse = $this->client->createRequirement($envelopeId, $signatureRequirement->toArray());
            $results['requirements'][] = $signatureReqResponse;

            // Create authentication requirement
            $authRequirement = Requirement::createAuthRequirement(
                signerId: $signerId,
                type: $requirementOptions['auth'] ?? 'email'
            );

            $authReqResponse = $this->client->createRequirement($envelopeId, $authRequirement->toArray());
            $results['requirements'][] = $authReqResponse;
        }

        return $results;
    }

    /**
     * Start the signature process (set envelope to running)
     */
    public function startSignatureProcess(string $envelopeId): array
    {
        $envelope = new Envelope(status: 'running');
        return $this->client->updateEnvelope($envelopeId, $envelope->toUpdateArray());
    }

    /**
     * Send notification to signers
     */
    public function sendNotification(string $envelopeId, ?string $message = null): array
    {
        $data = [];
        if ($message) {
            $data['message'] = $message;
        }
        return $this->client->sendNotification($envelopeId, $data);
    }

    /**
     * Create a template-based workflow
     */
    public function createTemplateWorkflow(
        string $envelopeName,
        string $filename,
        string $templateId,
        array $templateData,
        array $signers,
        array $envelopeOptions = [],
        array $requirementOptions = []
    ): array {
        // Step 1: Create envelope
        $envelope = new Envelope(
            name: $envelopeName,
            locale: $envelopeOptions['locale'] ?? 'pt-BR',
            autoClose: $envelopeOptions['auto_close'] ?? true,
            remindInterval: $envelopeOptions['remind_interval'] ?? 3,
            blockAfterRefusal: $envelopeOptions['block_after_refusal'] ?? true,
            deadlineAt: $envelopeOptions['deadline_at'] ?? null
        );

        $envelopeResponse = $this->client->createEnvelope($envelope->toArray());
        $envelopeId = $envelopeResponse['data']['id'];

        // Step 2: Create document from template
        $document = Document::fromTemplate($filename, $templateId, $templateData);
        $documentResponse = $this->client->createDocument($envelopeId, $document->toArray());
        $documentId = $documentResponse['data']['id'];

        $results = [
            'envelope' => $envelopeResponse,
            'document' => $documentResponse,
            'signers' => [],
            'requirements' => []
        ];

        // Step 3: Create signers and requirements
        foreach ($signers as $signerData) {
            // Create signer
            $signer = Signer::create(
                name: $signerData['name'],
                email: $signerData['email'],
                birthday: $signerData['birthday'] ?? null,
                hasDocumentation: $signerData['has_documentation'] ?? true,
                communicateEvents: $signerData['communicate_events'] ?? null
            );

            $signerResponse = $this->client->createSigner($envelopeId, $signer->toArray());
            $signerId = $signerResponse['data']['id'];
            $results['signers'][] = $signerResponse;

            // Create signature requirement
            $signatureRequirement = Requirement::createSignatureRequirement(
                documentId: $documentId,
                signerId: $signerId,
                type: $requirementOptions['type'] ?? 'click'
            );

            $signatureReqResponse = $this->client->createRequirement($envelopeId, $signatureRequirement->toArray());
            $results['requirements'][] = $signatureReqResponse;

            // Create authentication requirement
            $authRequirement = Requirement::createAuthRequirement(
                signerId: $signerId,
                type: $requirementOptions['auth'] ?? 'email'
            );

            $authReqResponse = $this->client->createRequirement($envelopeId, $authRequirement->toArray());
            $results['requirements'][] = $authReqResponse;
        }

        return $results;
    }

    /**
     * Get envelope status with signers and requirements
     */
    public function getEnvelopeStatus(string $envelopeId): array
    {
        return [
            'envelope' => $this->client->getEnvelope($envelopeId),
            'signers' => $this->client->listSigners($envelopeId),
            'requirements' => $this->client->listRequirements($envelopeId)
        ];
    }

    /**
     * Bulk update requirements (placeholder - implement based on your needs)
     */
    public function bulkUpdateRequirements(string $envelopeId, array $operations): array
    {
        // This would need to be implemented based on the specific bulk operations API
        // For now, we'll return a placeholder response
        return [
            'envelope_id' => $envelopeId,
            'operations' => $operations,
            'status' => 'not_implemented'
        ];
    }

    /**
     * Handle workflow errors gracefully
     */
    public function handleWorkflowError(\Throwable $error, ?string $envelopeId = null): array
    {
        $errorData = [
            'error' => true,
            'message' => $error->getMessage(),
            'type' => get_class($error)
        ];

        if ($envelopeId) {
            $errorData['envelope_id'] = $envelopeId;
        }

        return $errorData;
    }
}
