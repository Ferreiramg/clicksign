<?php

use Clicksign\Exceptions\DocumentNotFoundException;
use Clicksign\Http\ClicksignFake;

it('can fake document creation', function () {
    $fake = new ClicksignFake;

    $document = $fake->createDocument(['filename' => 'test.pdf']);

    expect($document)->toHaveKey('key');
    expect($document['filename'])->toBe('test.pdf');
    expect($document['status'])->toBe('running');
});

it('can add fake documents', function () {
    $fake = new ClicksignFake;

    $fake->addFakeDocument('doc-123', [
        'filename' => 'contract.pdf',
        'status' => 'closed',
    ]);

    $document = $fake->getDocument('doc-123');

    expect($document['key'])->toBe('doc-123');
    expect($document['filename'])->toBe('contract.pdf');
    expect($document['status'])->toBe('closed');
});

it('throws exception for non-existent document', function () {
    $fake = new ClicksignFake;

    expect(fn () => $fake->getDocument('non-existent'))
        ->toThrow(DocumentNotFoundException::class);
});

it('can simulate failures', function () {
    $fake = new ClicksignFake;
    $fake->shouldFail(true);

    expect(fn () => $fake->createDocument(['filename' => 'test.pdf']))
        ->toThrow(\Clicksign\Exceptions\ClicksignException::class);
});

it('can reset fake state', function () {
    $fake = new ClicksignFake;

    $fake->createDocument(['filename' => 'test.pdf']);
    expect($fake->getDocuments())->toHaveCount(1);

    $fake->reset();
    expect($fake->getDocuments())->toHaveCount(0);
});
