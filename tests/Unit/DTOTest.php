<?php

use Clicksign\DTO\Document;
use Clicksign\DTO\Envelope;
use Clicksign\DTO\Requirement;
use Clicksign\DTO\SignatureRequest;
use Clicksign\DTO\Signer;
use Clicksign\DTO\Template;

it('can create document dto from array', function () {
    $data = [
        'type' => 'documents',
        'id' => 'doc-123',
        'attributes' => [
            'filename' => 'contract.pdf',
            'status' => 'running',
            'uploaded_at' => '2023-01-01T00:00:00Z',
        ],
    ];

    $document = Document::fromArray($data);

    expect($document->id)->toBe('doc-123');
    expect($document->filename)->toBe('contract.pdf');
    expect($document->status)->toBe('running');
    expect($document->isPending())->toBeTrue();
    expect($document->isCompleted())->toBeFalse();
});

it('can create signer dto from array', function () {
    $data = [
        'type' => 'signers',
        'id' => 'signer-123',
        'attributes' => [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'status' => 'pending',
            'created_at' => '2023-01-01T00:00:00Z',
        ],
    ];

    $signer = Signer::fromArray($data);

    expect($signer->id)->toBe('signer-123');
    expect($signer->email)->toBe('john@example.com');
    expect($signer->name)->toBe('John Doe');
    expect($signer->isPending())->toBeTrue();
    expect($signer->hasSigned())->toBeFalse();
});

it('can create signer with partial data', function () {
    $data = [
        'type' => 'signers',
        'id' => 'signer-456',
        'attributes' => [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ],
    ];

    $signer = Signer::fromArray($data);

    expect($signer->id)->toBe('signer-456');
    expect($signer->email)->toBe('jane@example.com');
    expect($signer->name)->toBe('Jane Doe');
});

it('can convert signer to array', function () {
    $signer = new Signer(
        email: 'test@example.com',
        name: 'Test User'
    );

    $array = $signer->toArray();

    expect($array['type'])->toBe('signers');
    expect($array['attributes']['email'])->toBe('test@example.com');
    expect($array['attributes']['name'])->toBe('Test User');
});

it('can check signer status methods', function () {
    $signedSigner = Signer::fromArray([
        'type' => 'signers',
        'id' => 'signer-signed',
        'attributes' => [
            'email' => 'signed@example.com',
            'name' => 'Signed User',
            'status' => 'signed',
        ],
    ]);
    expect($signedSigner->hasSigned())->toBeTrue();
    expect($signedSigner->isPending())->toBeFalse();

    $pendingSigner = Signer::fromArray([
        'type' => 'signers',
        'id' => 'signer-pending',
        'attributes' => [
            'email' => 'pending@example.com',
            'name' => 'Pending User',
            'status' => 'pending',
        ],
    ]);
    expect($pendingSigner->hasSigned())->toBeFalse();
    expect($pendingSigner->isPending())->toBeTrue();
});

it('can create document with minimal data', function () {
    $data = [
        'type' => 'documents',
        'id' => 'doc-minimal',
        'attributes' => [
            'filename' => 'simple.pdf',
        ],
    ];

    $document = Document::fromArray($data);

    expect($document->id)->toBe('doc-minimal');
    expect($document->filename)->toBe('simple.pdf');
});

it('can convert document to array', function () {
    $document = new Document(filename: 'test.pdf', contentBase64: base64_encode('test content'));

    $array = $document->toArray();

    expect($array['type'])->toBe('documents');
    expect($array['attributes']['filename'])->toBe('test.pdf');
});

it('can check document status methods', function () {
    $completedDoc = Document::fromArray([
        'type' => 'documents',
        'id' => 'doc-1',
        'attributes' => [
            'filename' => 'test.pdf',
            'status' => 'completed',
        ],
    ]);
    expect($completedDoc->isCompleted())->toBeTrue();
    expect($completedDoc->isCancelled())->toBeFalse();
    expect($completedDoc->isPending())->toBeFalse();

    $cancelledDoc = Document::fromArray([
        'type' => 'documents',
        'id' => 'doc-2',
        'attributes' => [
            'filename' => 'test.pdf',
            'status' => 'cancelled',
        ],
    ]);
    expect($cancelledDoc->isCancelled())->toBeTrue();
    expect($cancelledDoc->isCompleted())->toBeFalse();

    $pendingDoc = Document::fromArray([
        'type' => 'documents',
        'id' => 'doc-3',
        'attributes' => [
            'filename' => 'test.pdf',
            'status' => 'running',
        ],
    ]);
    expect($pendingDoc->isPending())->toBeTrue();
    expect($pendingDoc->isCompleted())->toBeFalse();
});

it('can create envelope', function () {
    $envelope = new Envelope(name: 'Contract Envelope');

    expect($envelope->name)->toBe('Contract Envelope');
});

it('can create requirement', function () {
    $requirement = new Requirement(
        action: 'sign',
        signerId: 'signer-456'
    );

    expect($requirement->action)->toBe('sign');
    expect($requirement->signerId)->toBe('signer-456');
});

it('can create template', function () {
    $template = new Template(name: 'Contract Template');

    expect($template->name)->toBe('Contract Template');
});

it('can build signature request', function () {
    $request = new SignatureRequest(documentPath: '/path/to/document.pdf', filename: 'contract.pdf');

    $request = $request->addSigner('john@example.com', 'John Doe');
    $request = $request->addSigner('jane@example.com', 'Jane Doe');

    expect($request->signers)->toHaveCount(2);
});

it('can add signers to signature request', function () {
    $request = new SignatureRequest(documentPath: '/path/test.pdf', filename: 'test.pdf');

    $request = $request->addSigner('test1@example.com', 'Test User 1');
    $request = $request->addSigner('test2@example.com', 'Test User 2');

    expect($request->signers)->toHaveCount(2);
    expect($request->signers[0]['email'])->toBe('test1@example.com');
});

it('can create signature request', function () {
    $request = new SignatureRequest(documentPath: '/path/to/document.pdf', filename: 'contract.pdf');

    expect($request->filename)->toBe('contract.pdf');
    expect($request->signers)->toBeEmpty();
});
