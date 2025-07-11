<?php

use Clicksign\Http\ClicksignFake;

it('can fake envelope creation', function () {
    $fake = new ClicksignFake;

    $envelope = $fake->createEnvelope([
        'type' => 'envelopes',
        'attributes' => ['name' => 'Test Envelope'],
    ]);

    expect($envelope)->toHaveKey('data');
    expect($envelope['data'])->toHaveKey('id');
    expect($envelope['data']['type'])->toBe('envelopes');
    expect($envelope['data']['attributes']['name'])->toBe('Test Envelope');
});

it('can fake document creation in envelope', function () {
    $fake = new ClicksignFake;

    $envelope = $fake->createEnvelope([
        'type' => 'envelopes',
        'attributes' => ['name' => 'Test Envelope'],
    ]);
    $envelopeId = $envelope['data']['id'];

    $document = $fake->createDocument($envelopeId, [
        'type' => 'documents',
        'attributes' => ['filename' => 'test.pdf'],
    ]);

    expect($document)->toHaveKey('data');
    expect($document['data'])->toHaveKey('id');
    expect($document['data']['type'])->toBe('documents');
    expect($document['data']['attributes']['filename'])->toBe('test.pdf');
});

it('can fake signer creation', function () {
    $fake = new ClicksignFake;

    $envelope = $fake->createEnvelope([
        'type' => 'envelopes',
        'attributes' => ['name' => 'Test Envelope'],
    ]);
    $envelopeId = $envelope['data']['id'];

    $signer = $fake->createSigner($envelopeId, [
        'type' => 'signers',
        'attributes' => [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ],
    ]);

    expect($signer)->toHaveKey('data');
    expect($signer['data'])->toHaveKey('id');
    expect($signer['data']['type'])->toBe('signers');
    expect($signer['data']['attributes']['name'])->toBe('John Doe');
    expect($signer['data']['attributes']['email'])->toBe('john@example.com');
});

it('can fake requirement creation', function () {
    $fake = new ClicksignFake;

    $envelope = $fake->createEnvelope([
        'type' => 'envelopes',
        'attributes' => ['name' => 'Test Envelope'],
    ]);
    $envelopeId = $envelope['data']['id'];

    $requirement = $fake->createRequirement($envelopeId, [
        'type' => 'requirements',
        'attributes' => [
            'action' => 'agree',
            'role' => 'sign',
        ],
    ]);

    expect($requirement)->toHaveKey('data');
    expect($requirement['data'])->toHaveKey('id');
    expect($requirement['data']['type'])->toBe('requirements');
    expect($requirement['data']['attributes']['action'])->toBe('agree');
    expect($requirement['data']['attributes']['role'])->toBe('sign');
});

it('throws exception for non-existent envelope', function () {
    $fake = new ClicksignFake;

    expect(fn () => $fake->getEnvelope('non-existent'))
        ->toThrow(\Exception::class);
});

it('can simulate failures', function () {
    $fake = new ClicksignFake;
    $fake->shouldFail(true);

    expect(fn () => $fake->createEnvelope([
        'type' => 'envelopes',
        'attributes' => ['name' => 'Test'],
    ]))->toThrow(\Exception::class);
});

it('can reset fake state', function () {
    $fake = new ClicksignFake;

    $fake->createEnvelope([
        'type' => 'envelopes',
        'attributes' => ['name' => 'Test'],
    ]);

    $envelopes = $fake->listEnvelopes();
    expect($envelopes['data'])->toHaveCount(1);

    $fake->reset();

    $envelopes = $fake->listEnvelopes();
    expect($envelopes['data'])->toHaveCount(0);
});

it('can fake template operations', function () {
    $fake = new ClicksignFake;

    $template = $fake->createTemplate([
        'type' => 'templates',
        'attributes' => [
            'name' => 'Contract Template',
            'content_base64' => 'fake_base64_content',
        ],
    ]);

    expect($template)->toHaveKey('data');
    expect($template['data'])->toHaveKey('id');
    expect($template['data']['type'])->toBe('templates');
    expect($template['data']['attributes']['name'])->toBe('Contract Template');

    $templateId = $template['data']['id'];
    $retrieved = $fake->getTemplate($templateId);
    expect($retrieved['data']['id'])->toBe($templateId);
});

it('can fake notification sending', function () {
    $fake = new ClicksignFake;

    $envelope = $fake->createEnvelope([
        'type' => 'envelopes',
        'attributes' => ['name' => 'Test Envelope'],
    ]);
    $envelopeId = $envelope['data']['id'];

    $notification = $fake->sendNotification($envelopeId, [
        'type' => 'notifications',
        'attributes' => ['message' => 'Test message'],
    ]);

    expect($notification)->toHaveKey('data');
    expect($notification['data'])->toHaveKey('id');
    expect($notification['data']['type'])->toBe('notifications');
    expect($notification['data']['attributes']['status'])->toBe('sent');
});
