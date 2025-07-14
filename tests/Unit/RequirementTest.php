<?php

use Clicksign\DTO\Requirement;

it('can create signature requirement with custom role', function () {
    $requirement = Requirement::createSignatureRequirement('doc-123', 'signer-456', 'draw');

    expect($requirement->action)->toBe('agree');
    expect($requirement->documentId)->toBe('doc-123');
    expect($requirement->signerId)->toBe('signer-456');
    expect($requirement->role)->toBe('draw');
});

it('can create auth requirement with phone', function () {
    $requirement = Requirement::createAuthRequirement('signer-789', 'phone');

    expect($requirement->action)->toBe('provide_evidence');
    expect($requirement->signerId)->toBe('signer-789');
    expect($requirement->auth)->toBe('phone');
});

it('can create requirement from array', function () {
    $data = [
        'type' => 'requirements',
        'id' => 'req-123',
        'attributes' => [
            'action' => 'sign',
            'role' => 'click',
        ],
        'relationships' => [
            'document' => ['data' => ['id' => 'doc-123']],
            'signer' => ['data' => ['id' => 'signer-456']],
        ],
    ];

    $requirement = Requirement::fromArray($data);

    expect($requirement->id)->toBe('req-123');
    expect($requirement->action)->toBe('sign');
    expect($requirement->role)->toBe('click');
    expect($requirement->documentId)->toBe('doc-123');
    expect($requirement->signerId)->toBe('signer-456');
});

it('can convert requirement to array', function () {
    $requirement = new Requirement(
        action: 'sign',
        documentId: 'doc-123',
        signerId: 'signer-456',
        role: 'click'
    );

    $array = $requirement->toArray();

    expect($array['type'])->toBe('requirements');
    expect($array['attributes']['action'])->toBe('sign');
    expect($array['attributes']['role'])->toBe('click');
    expect($array['relationships']['document']['data']['id'])->toBe('doc-123');
    expect($array['relationships']['signer']['data']['id'])->toBe('signer-456');
});

it('can create requirement with metadata', function () {
    $requirement = new Requirement(
        action: 'approve',
        signerId: 'signer-123',
        auth: 'sms'
    );

    $array = $requirement->toArray();
    expect($array['attributes']['action'])->toBe('approve');
});

it('can handle requirement without document id', function () {
    $requirement = Requirement::createAuthRequirement('signer-123', 'email');

    expect($requirement->documentId)->toBeNull();
    expect($requirement->signerId)->toBe('signer-123');
    expect($requirement->action)->toBe('provide_evidence');
});

it('can check if requirement is signature type', function () {
    $signRequirement = Requirement::createSignatureRequirement('doc-1', 'signer-1', 'click');
    $authRequirement = Requirement::createAuthRequirement('signer-1', 'sms');

    expect($signRequirement->isSignatureRequirement())->toBeTrue();
    expect($signRequirement->isAuthRequirement())->toBeFalse();

    expect($authRequirement->isSignatureRequirement())->toBeFalse();
    expect($authRequirement->isAuthRequirement())->toBeTrue();
});

it('can check if requirement is auth type', function () {
    $authRequirement = Requirement::createAuthRequirement('signer-1', 'doc');

    expect($authRequirement->isAuthRequirement())->toBeTrue();
    expect($authRequirement->isSignatureRequirement())->toBeFalse();
});

it('can get requirement status', function () {
    $requirement = Requirement::fromArray([
        'type' => 'requirements',
        'id' => 'req-123',
        'attributes' => [
            'action' => 'sign',
            'status' => 'completed',
        ],
    ]);

    expect($requirement->isCompleted())->toBeTrue();
    expect($requirement->isPending())->toBeFalse();
});

it('can handle different requirement statuses', function () {
    $pendingReq = Requirement::fromArray([
        'type' => 'requirements',
        'id' => 'req-1',
        'attributes' => ['status' => 'pending'],
    ]);

    $completedReq = Requirement::fromArray([
        'type' => 'requirements',
        'id' => 'req-2',
        'attributes' => ['status' => 'completed'],
    ]);

    expect($pendingReq->isPending())->toBeTrue();
    expect($pendingReq->isCompleted())->toBeFalse();

    expect($completedReq->isPending())->toBeFalse();
    expect($completedReq->isCompleted())->toBeTrue();
});
