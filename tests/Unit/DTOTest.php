<?php

use Clicksign\DTO\Document;
use Clicksign\DTO\SignatureRequest;
use Clicksign\DTO\Signer;

it('can create document dto from array', function () {
    $data = [
        'key' => 'doc-123',
        'filename' => 'contract.pdf',
        'status' => 'running',
        'uploaded_at' => '2023-01-01T00:00:00Z',
        'signers' => [],
        'events' => [],
    ];

    $document = Document::fromArray($data);

    expect($document->key)->toBe('doc-123');
    expect($document->filename)->toBe('contract.pdf');
    expect($document->status)->toBe('running');
    expect($document->isPending())->toBeTrue();
    expect($document->isCompleted())->toBeFalse();
});

it('can create signer dto from array', function () {
    $data = [
        'key' => 'signer-123',
        'email' => 'john@example.com',
        'name' => 'John Doe',
        'status' => 'pending',
        'created_at' => '2023-01-01T00:00:00Z',
    ];

    $signer = Signer::fromArray($data);

    expect($signer->key)->toBe('signer-123');
    expect($signer->email)->toBe('john@example.com');
    expect($signer->name)->toBe('John Doe');
    expect($signer->isPending())->toBeTrue();
    expect($signer->hasSigned())->toBeFalse();
});

it('can build signature request', function () {
    $request = SignatureRequest::create('/path/to/document.pdf', 'contract.pdf')
        ->addSigner('john@example.com', 'John Doe', '12345678901')
        ->addSigner('jane@example.com', 'Jane Doe')
        ->withMessage('Please sign this contract')
        ->ordered()
        ->skipEmail();

    $data = $request->toArray();

    expect($data['filename'])->toBe('contract.pdf');
    expect($data['signers'])->toHaveCount(2);
    expect($data['message'])->toBe('Please sign this contract');
    expect($data['ordered'])->toBeTrue();
    expect($data['skip_email'])->toBeTrue();
    expect($data['signers'][0]['email'])->toBe('john@example.com');
    expect($data['signers'][0]['documentation'])->toBe('12345678901');
});

it('can create signer with partial data', function () {
    $data = [
        'email' => 'jane@example.com',
        'name' => 'Jane Doe',
    ];

    $signer = Signer::fromArray($data);

    expect($signer->key)->toBe('');
    expect($signer->email)->toBe('jane@example.com');
    expect($signer->name)->toBe('Jane Doe');
    expect($signer->documentationNumber)->toBeNull();
    expect($signer->birthday)->toBeNull();
    expect($signer->phoneNumber)->toBeNull();
    expect($signer->events)->toBe([]);
    expect($signer->deliveryMethod)->toBeNull();
});

it('can convert signer to array', function () {
    $signer = new Signer(
        key: 'signer-456',
        email: 'test@example.com',
        name: 'Test User',
        documentationNumber: '98765432100',
        birthday: '1985-05-15',
        phoneNumber: '+5511888888888',
        deliveryMethod: 'sms'
    );

    $array = $signer->toArray();

    expect($array)->toHaveKey('key');
    expect($array)->toHaveKey('email');
    expect($array)->toHaveKey('name');
    expect($array['key'])->toBe('signer-456');
    expect($array['email'])->toBe('test@example.com');
    expect($array['name'])->toBe('Test User');
});

it('can check signer status methods', function () {
    $signedSigner = new Signer(
        key: 'signer-signed',
        email: 'signed@example.com',
        name: 'Signed User',
        status: 'signed'
    );

    expect($signedSigner->hasSigned())->toBeTrue();
    expect($signedSigner->isPending())->toBeFalse();
    expect($signedSigner->isViewing())->toBeFalse();

    $pendingSigner = new Signer(
        key: 'signer-pending',
        email: 'pending@example.com',
        name: 'Pending User',
        status: 'pending'
    );

    expect($pendingSigner->hasSigned())->toBeFalse();
    expect($pendingSigner->isPending())->toBeTrue();
    expect($pendingSigner->isViewing())->toBeFalse();

    $viewingSigner = new Signer(
        key: 'signer-viewing',
        email: 'viewing@example.com',
        name: 'Viewing User',
        status: 'viewing'
    );

    expect($viewingSigner->hasSigned())->toBeFalse();
    expect($viewingSigner->isPending())->toBeFalse();
    expect($viewingSigner->isViewing())->toBeTrue();
});

it('can create document with minimal data', function () {
    $data = [
        'key' => 'doc-minimal',
        'filename' => 'simple.pdf',
    ];

    $document = Document::fromArray($data);

    expect($document->key)->toBe('doc-minimal');
    expect($document->filename)->toBe('simple.pdf');
    expect($document->uploadedAt)->toBeNull();
    expect($document->status)->toBeNull();
    expect($document->signers)->toBe([]);
    expect($document->events)->toBe([]);
    expect($document->metadata)->toBe([]);
});

it('can convert document to array', function () {
    $document = new Document(
        key: 'doc-array-test',
        filename: 'test.pdf',
        uploadedAt: '2024-01-01T10:00:00Z',
        updatedAt: '2024-01-01T11:00:00Z',
        finishedAt: '2024-01-01T12:00:00Z',
        status: 'running',
        signers: [['key' => 'signer-1']],
        events: [['type' => 'created']],
        downloadUrl: 'https://example.com/download',
        metadata: ['custom' => 'data']
    );

    $array = $document->toArray();

    expect($array)->toBe([
        'key' => 'doc-array-test',
        'filename' => 'test.pdf',
        'uploaded_at' => '2024-01-01T10:00:00Z',
        'updated_at' => '2024-01-01T11:00:00Z',
        'finished_at' => '2024-01-01T12:00:00Z',
        'status' => 'running',
        'signers' => [['key' => 'signer-1']],
        'events' => [['type' => 'created']],
        'download_url' => 'https://example.com/download',
        'metadata' => ['custom' => 'data'],
    ]);
});

it('can check document status methods', function () {
    $completedDoc = new Document(key: 'doc-1', filename: 'test.pdf', status: 'closed');
    expect($completedDoc->isCompleted())->toBeTrue();
    expect($completedDoc->isCancelled())->toBeFalse();
    expect($completedDoc->isPending())->toBeFalse();

    $cancelledDoc = new Document(key: 'doc-2', filename: 'test.pdf', status: 'cancelled');
    expect($cancelledDoc->isCompleted())->toBeFalse();
    expect($cancelledDoc->isCancelled())->toBeTrue();
    expect($cancelledDoc->isPending())->toBeFalse();

    $pendingDoc = new Document(key: 'doc-3', filename: 'test.pdf', status: 'running');
    expect($pendingDoc->isCompleted())->toBeFalse();
    expect($pendingDoc->isCancelled())->toBeFalse();
    expect($pendingDoc->isPending())->toBeTrue();

    $unknownDoc = new Document(key: 'doc-4', filename: 'test.pdf', status: 'unknown');
    expect($unknownDoc->isCompleted())->toBeFalse();
    expect($unknownDoc->isCancelled())->toBeFalse();
    expect($unknownDoc->isPending())->toBeFalse();
});

it('can create signature request', function () {
    $request = SignatureRequest::create('/path/to/document.pdf', 'contract.pdf');

    expect($request->documentPath)->toBe('/path/to/document.pdf');
    expect($request->filename)->toBe('contract.pdf');
    expect($request->signers)->toBe([]);
    expect($request->skipEmail)->toBeFalse();
    expect($request->ordered)->toBeFalse();
});

it('can add signers to signature request', function () {
    $request = SignatureRequest::create('/path/to/document.pdf', 'contract.pdf');

    $requestWithSigner = $request->addSigner(
        email: 'john@example.com',
        name: 'John Doe',
        documentationNumber: '12345678901',
        deliveryMethod: 'email'
    );

    expect($requestWithSigner->signers)->toHaveCount(1);
    expect($requestWithSigner->signers[0]['email'])->toBe('john@example.com');
    expect($requestWithSigner->signers[0]['name'])->toBe('John Doe');
    expect($requestWithSigner->signers[0]['documentation'])->toBe('12345678901');
});
