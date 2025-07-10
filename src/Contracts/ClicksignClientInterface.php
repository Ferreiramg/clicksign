<?php

namespace Clicksign\Contracts;

interface ClicksignClientInterface
{
    /**
     * Create a new document
     */
    public function createDocument(array $data): array;

    /**
     * Get document details
     */
    public function getDocument(string $key): array;

    /**
     * List all documents
     */
    public function listDocuments(array $filters = []): array;

    /**
     * Add a signer to a document
     */
    public function addSigner(string $documentKey, array $signerData): array;

    /**
     * Remove a signer from a document
     */
    public function removeSigner(string $documentKey, string $signerKey): array;

    /**
     * Get document download URL
     */
    public function getDownloadUrl(string $documentKey): string;

    /**
     * Cancel a document
     */
    public function cancelDocument(string $documentKey): array;

    /**
     * Resend notification to signers
     */
    public function resendNotification(string $documentKey, array $signerKeys = []): array;
}
