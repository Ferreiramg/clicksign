<?php

use Clicksign\Facades\Clicksign;
use Clicksign\Http\ClicksignFake;

beforeEach(function () {
    // Use fake client for testing
    $this->app->bind(\Clicksign\Contracts\ClicksignClientInterface::class, ClicksignFake::class);
});

it('can create document', function () {
    $response = Clicksign::createDocument([
        'filename' => 'test-document.pdf',
        'content' => base64_encode('fake pdf content'),
    ]);

    expect($response)->toHaveKey('key');
    expect($response)->toHaveKey('filename');
    expect($response['filename'])->toBe('test-document.pdf');
});

it('can get document', function () {
    // First create a document
    $document = Clicksign::createDocument([
        'filename' => 'test-document.pdf',
        'content' => base64_encode('fake pdf content'),
    ]);

    // Then retrieve it
    $retrievedDocument = Clicksign::getDocument($document['key']);

    expect($retrievedDocument['key'])->toBe($document['key']);
    expect($retrievedDocument['filename'])->toBe($document['filename']);
});

it('can list documents', function () {
    // Create some documents
    Clicksign::createDocument(['filename' => 'doc1.pdf']);
    Clicksign::createDocument(['filename' => 'doc2.pdf']);

    $documents = Clicksign::listDocuments();

    expect($documents)->toBeArray();
    expect($documents)->toHaveCount(2);
});

it('can add signer', function () {
    $document = Clicksign::createDocument([
        'filename' => 'test-document.pdf',
    ]);

    $signer = Clicksign::addSigner($document['key'], [
        'email' => 'signer@example.com',
        'name' => 'John Doe',
    ]);

    expect($signer)->toHaveKey('key');
    expect($signer['email'])->toBe('signer@example.com');
    expect($signer['name'])->toBe('John Doe');
});

it('can get download url', function () {
    $document = Clicksign::createDocument([
        'filename' => 'test-document.pdf',
    ]);

    $downloadUrl = Clicksign::getDownloadUrl($document['key']);

    expect($downloadUrl)->toBeString();
    expect($downloadUrl)->toContain('download');
});

it('can cancel document', function () {
    $document = Clicksign::createDocument([
        'filename' => 'test-document.pdf',
    ]);

    $cancelledDocument = Clicksign::cancelDocument($document['key']);

    expect($cancelledDocument['status'])->toBe('cancelled');
});
