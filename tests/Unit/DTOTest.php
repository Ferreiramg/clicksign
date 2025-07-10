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
