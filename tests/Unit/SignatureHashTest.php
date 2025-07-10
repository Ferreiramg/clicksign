<?php

use Clicksign\Support\SignatureHash;

it('can generate signature hash', function () {
    $data = 'test data';
    $secret = 'test-secret';

    $hash = SignatureHash::generate($data, $secret);

    expect($hash)->toBeString();
    expect(strlen($hash))->toBe(64); // SHA256 hash length
});

it('can verify signature hash', function () {
    $data = 'test data';
    $secret = 'test-secret';

    $hash = SignatureHash::generate($data, $secret);

    expect(SignatureHash::verify($data, $hash, $secret))->toBeTrue();
    expect(SignatureHash::verify($data, 'invalid-hash', $secret))->toBeFalse();
    expect(SignatureHash::verify('different data', $hash, $secret))->toBeFalse();
});

it('can generate request signature', function () {
    $method = 'POST';
    $uri = '/api/documents';
    $body = '{"filename":"test.pdf"}';
    $timestamp = '1640995200';
    $secret = 'test-secret';

    $signature = SignatureHash::generateRequestSignature($method, $uri, $body, $timestamp, $secret);

    expect($signature)->toBeString();
    expect(strlen($signature))->toBe(64);
});

it('can verify request signature', function () {
    $method = 'POST';
    $uri = '/api/documents';
    $body = '{"filename":"test.pdf"}';
    $timestamp = '1640995200';
    $secret = 'test-secret';

    $signature = SignatureHash::generateRequestSignature($method, $uri, $body, $timestamp, $secret);

    expect(SignatureHash::verifyRequestSignature($method, $uri, $body, $timestamp, $signature, $secret))->toBeTrue();
    expect(SignatureHash::verifyRequestSignature('GET', $uri, $body, $timestamp, $signature, $secret))->toBeFalse();
});
