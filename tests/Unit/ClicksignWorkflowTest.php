<?php

use Clicksign\DTO\Envelope;
use Clicksign\Http\ClicksignFake;
use Clicksign\Support\ClicksignWorkflow;

beforeEach(function () {
    $this->client = new ClicksignFake;
    $this->workflow = new ClicksignWorkflow($this->client);
});

it('can create complete signature workflow', function () {
    $result = $this->workflow->createSignatureWorkflow(
        'Test Contract', // envelopeName
        'contract.pdf', // filename
        base64_encode('fake pdf content'), // contentBase64
        [ // signers
            [
                'email' => 'john@example.com',
                'name' => 'John Doe',
                'documentation_number' => '12345678901',
            ],
            [
                'email' => 'jane@example.com',
                'name' => 'Jane Doe',
            ],
        ]
    );

    expect($result)->toHaveKey('envelope');
    expect($result)->toHaveKey('document');
    expect($result)->toHaveKey('signers');
    expect($result)->toHaveKey('requirements');
    expect($result['envelope']['data']['attributes']['name'])->toBe('Test Contract');
    expect($result['signers'])->toHaveCount(2);
});

it('can create template workflow', function () {
    $result = $this->workflow->createTemplateWorkflow(
        'Template Contract', // envelopeName
        'generated_contract.pdf', // filename
        'template-123', // templateId
        ['company_name' => 'ACME Corp'], // templateData
        [ // signers
            [
                'email' => 'client@example.com',
                'name' => 'Client Name',
            ],
        ]
    );

    expect($result)->toHaveKey('envelope');
    expect($result)->toHaveKey('document');
    expect($result)->toHaveKey('signers');
    expect($result['envelope']['data']['attributes']['name'])->toBe('Template Contract');
});

it('can start signature process', function () {
    // First create an envelope
    $envelope = new Envelope(name: 'Test Envelope');
    $envelopeResponse = $this->client->createEnvelope($envelope->toArray());
    $envelopeId = $envelopeResponse['data']['id'];

    $result = $this->workflow->startSignatureProcess($envelopeId);

    expect($result)->toHaveKey('data');
});

it('can create workflow with custom options', function () {
    $result = $this->workflow->createSignatureWorkflow(
        'Custom Envelope',
        'custom.pdf',
        base64_encode('custom content'),
        [
            ['email' => 'test@example.com', 'name' => 'Test User'],
        ],
        ['locale' => 'en-US', 'auto_close' => false], // envelope options
        ['role' => 'sign'] // requirement options
    );

    expect($result)->toHaveKey('envelope');
    expect($result['envelope']['data']['attributes']['name'])->toBe('Custom Envelope');
});

it('handles workflow errors gracefully', function () {
    $this->client->shouldFail();

    expect(fn () => $this->workflow->createSignatureWorkflow(
        'Test',
        'test.pdf',
        base64_encode('content'),
        []
    ))->toThrow(\Exception::class);
});
