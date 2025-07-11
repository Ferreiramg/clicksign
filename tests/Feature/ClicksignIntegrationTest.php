<?php

use Clicksign\DTO\Document;
use Clicksign\DTO\Envelope;
use Clicksign\DTO\Signer;
use Clicksign\Facades\Clicksign;
use Clicksign\Http\ClicksignFake;

beforeEach(function () {
    // Use fake client for testing
    $this->app->bind(\Clicksign\Contracts\ClicksignClientInterface::class, ClicksignFake::class);
});

it('can create envelope', function () {
    $envelope = new Envelope(name: 'Test Envelope');

    $response = Clicksign::createEnvelope($envelope->toArray());

    expect($response)->toHaveKey('data');
    expect($response['data'])->toHaveKey('id');
    expect($response['data']['type'])->toBe('envelopes');
    expect($response['data']['attributes']['name'])->toBe('Test Envelope');
});

it('can get envelope', function () {
    // First create an envelope
    $envelope = new Envelope(name: 'Test Envelope');
    $createResponse = Clicksign::createEnvelope($envelope->toArray());
    $envelopeId = $createResponse['data']['id'];

    // Then retrieve it
    $response = Clicksign::getEnvelope($envelopeId);

    expect($response['data']['id'])->toBe($envelopeId);
    expect($response['data']['type'])->toBe('envelopes');
});

it('can list envelopes', function () {
    // Create some envelopes
    $envelope1 = new Envelope(name: 'Envelope 1');
    $envelope2 = new Envelope(name: 'Envelope 2');

    Clicksign::createEnvelope($envelope1->toArray());
    Clicksign::createEnvelope($envelope2->toArray());

    $response = Clicksign::listEnvelopes();

    expect($response)->toHaveKey('data');
    expect($response['data'])->toBeArray();
});

it('can create document in envelope', function () {
    // First create an envelope
    $envelope = new Envelope(name: 'Test Envelope');
    $envelopeResponse = Clicksign::createEnvelope($envelope->toArray());
    $envelopeId = $envelopeResponse['data']['id'];

    // Then create a document
    $document = new Document(filename: 'test.pdf', contentBase64: base64_encode('test pdf content'));
    $response = Clicksign::createDocument($envelopeId, $document->toArray());

    expect($response)->toHaveKey('data');
    expect($response['data']['type'])->toBe('documents');
    expect($response['data']['attributes']['filename'])->toBe('test.pdf');
});

it('can create signer in envelope', function () {
    // First create an envelope
    $envelope = new Envelope(name: 'Test Envelope');
    $envelopeResponse = Clicksign::createEnvelope($envelope->toArray());
    $envelopeId = $envelopeResponse['data']['id'];

    // Then create a signer
    $signer = new Signer(email: 'test@example.com', name: 'Test User');
    $response = Clicksign::createSigner($envelopeId, $signer->toArray());

    expect($response)->toHaveKey('data');
    expect($response['data']['type'])->toBe('signers');
    expect($response['data']['attributes']['email'])->toBe('test@example.com');
});

it('can send notifications', function () {
    // First create an envelope
    $envelope = new Envelope(name: 'Test Envelope');
    $envelopeResponse = Clicksign::createEnvelope($envelope->toArray());
    $envelopeId = $envelopeResponse['data']['id'];

    // Send notifications
    $response = Clicksign::sendNotifications($envelopeId, [
        'message' => 'Please sign the document',
    ]);

    expect($response)->toHaveKey('data');
    expect($response['data']['type'])->toBe('notifications');
});
