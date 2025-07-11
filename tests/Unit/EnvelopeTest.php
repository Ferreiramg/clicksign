<?php

use Clicksign\DTO\Envelope;
use Clicksign\DTO\Document;
use Clicksign\DTO\Signer;

it('can create envelope with all parameters', function () {
    $envelope = new Envelope(
        name: 'Complete Envelope',
        locale: 'pt-BR',
        autoClose: true,
        remindInterval: 7,
        blockAfterRefusal: true,
        deadlineAt: '2024-12-31T23:59:59Z'
    );

    expect($envelope->name)->toBe('Complete Envelope');
    expect($envelope->locale)->toBe('pt-BR');
    expect($envelope->autoClose)->toBeTrue();
    expect($envelope->remindInterval)->toBe(7);
    expect($envelope->blockAfterRefusal)->toBeTrue();
    expect($envelope->deadlineAt)->toBe('2024-12-31T23:59:59Z');
});

it('can create envelope from array with relationships', function () {
    $data = [
        'type' => 'envelopes',
        'id' => 'envelope-123',
        'attributes' => [
            'name' => 'Contract Envelope',
            'locale' => 'en-US',
            'auto_close' => false,
            'remind_interval' => 14,
            'status' => 'draft',
            'created_at' => '2024-01-01T00:00:00Z'
        ],
        'relationships' => [
            'documents' => [
                'data' => [
                    ['type' => 'documents', 'id' => 'doc-1'],
                    ['type' => 'documents', 'id' => 'doc-2']
                ]
            ],
            'signers' => [
                'data' => [
                    ['type' => 'signers', 'id' => 'signer-1']
                ]
            ]
        ]
    ];

    $envelope = Envelope::fromArray($data);

    expect($envelope->id)->toBe('envelope-123');
    expect($envelope->name)->toBe('Contract Envelope');
    expect($envelope->locale)->toBe('en-US');
    expect($envelope->autoClose)->toBeFalse();
    expect($envelope->remindInterval)->toBe(14);
    expect($envelope->status)->toBe('draft');
    expect($envelope->documents)->toHaveCount(2);
    expect($envelope->signers)->toHaveCount(1);
});

it('can convert envelope to array', function () {
    $envelope = new Envelope(
        name: 'Test Envelope',
        locale: 'pt-BR',
        autoClose: true,
        metadata: ['priority' => 'high']
    );

    $array = $envelope->toArray();

    expect($array['type'])->toBe('envelopes');
    expect($array['attributes']['name'])->toBe('Test Envelope');
    expect($array['attributes']['locale'])->toBe('pt-BR');
    expect($array['attributes']['auto_close'])->toBeTrue();
    expect($array['attributes']['metadata'])->toBe(['priority' => 'high']);
});

it('can convert envelope to update array', function () {
    $envelope = new Envelope(
        id: 'envelope-456',
        name: 'Updated Envelope',
        status: 'sent'
    );

    $array = $envelope->toUpdateArray();

    expect($array['type'])->toBe('envelopes');
    expect($array['attributes']['name'])->toBe('Updated Envelope');
    expect($array['attributes']['status'])->toBe('sent');
});

it('can add document to envelope', function () {
    $envelope = new Envelope(name: 'Document Test');
    $document = new Document(filename: 'test.pdf');

    $updatedEnvelope = $envelope->addDocument($document);

    expect($updatedEnvelope->documents)->toHaveCount(1);
    expect($updatedEnvelope->documents[0])->toBe($document);
});

it('can add signer to envelope', function () {
    $envelope = new Envelope(name: 'Signer Test');
    $signer = new Signer(email: 'test@example.com', name: 'Test User');

    $updatedEnvelope = $envelope->addSigner($signer);

    expect($updatedEnvelope->signers)->toHaveCount(1);
    expect($updatedEnvelope->signers[0])->toBe($signer);
});

it('can add multiple documents and signers', function () {
    $envelope = new Envelope(name: 'Multi Test');
    
    $doc1 = new Document(filename: 'doc1.pdf');
    $doc2 = new Document(filename: 'doc2.pdf');
    $signer1 = new Signer(email: 'user1@example.com', name: 'User 1');
    $signer2 = new Signer(email: 'user2@example.com', name: 'User 2');

    $updatedEnvelope = $envelope
        ->addDocument($doc1)
        ->addDocument($doc2)
        ->addSigner($signer1)
        ->addSigner($signer2);

    expect($updatedEnvelope->documents)->toHaveCount(2);
    expect($updatedEnvelope->signers)->toHaveCount(2);
});

it('can check envelope status', function () {
    $draftEnvelope = new Envelope(name: 'Draft', status: 'draft');
    $sentEnvelope = new Envelope(name: 'Sent', status: 'sent');
    $completedEnvelope = new Envelope(name: 'Completed', status: 'completed');

    expect($draftEnvelope->isDraft())->toBeTrue();
    expect($draftEnvelope->isSent())->toBeFalse();
    expect($draftEnvelope->isCompleted())->toBeFalse();

    expect($sentEnvelope->isDraft())->toBeFalse();
    expect($sentEnvelope->isSent())->toBeTrue();
    expect($sentEnvelope->isCompleted())->toBeFalse();

    expect($completedEnvelope->isDraft())->toBeFalse();
    expect($completedEnvelope->isSent())->toBeFalse();
    expect($completedEnvelope->isCompleted())->toBeTrue();
});

it('can check if envelope has deadline', function () {
    $envelopeWithDeadline = new Envelope(
        name: 'Deadline Test',
        deadlineAt: '2024-12-31T23:59:59Z'
    );

    $envelopeWithoutDeadline = new Envelope(name: 'No Deadline');

    expect($envelopeWithDeadline->hasDeadline())->toBeTrue();
    expect($envelopeWithoutDeadline->hasDeadline())->toBeFalse();
});

it('can check if envelope has reminders enabled', function () {
    $envelopeWithReminders = new Envelope(
        name: 'Reminders Test',
        remindInterval: 7
    );

    $envelopeWithoutReminders = new Envelope(name: 'No Reminders');

    expect($envelopeWithReminders->hasReminders())->toBeTrue();
    expect($envelopeWithoutReminders->hasReminders())->toBeFalse();
});

it('can get envelope progress percentage', function () {
    $envelope = new Envelope(
        name: 'Progress Test',
        signers: [
            ['status' => 'signed'],
            ['status' => 'signed'], 
            ['status' => 'pending'],
            ['status' => 'pending']
        ]
    );

    $progress = $envelope->getProgressPercentage();

    expect($progress)->toBe(50.0); // 2 out of 4 signed
});

it('handles envelope with no signers for progress', function () {
    $envelope = new Envelope(name: 'No Signers');

    $progress = $envelope->getProgressPercentage();

    expect($progress)->toBe(0.0);
});
