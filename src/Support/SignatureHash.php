<?php

namespace Clicksign\Support;

class SignatureHash
{
    /**
     * Generate a signature hash for webhook validation
     */
    public static function generate(string $data, string $secret): string
    {
        return hash_hmac('sha256', $data, $secret);
    }

    /**
     * Verify webhook signature
     */
    public static function verify(string $data, string $signature, string $secret): bool
    {
        $expectedSignature = self::generate($data, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Generate a request signature for API calls
     */
    public static function generateRequestSignature(string $method, string $uri, string $body, string $timestamp, string $secret): string
    {
        $stringToSign = strtoupper($method).'|'.$uri.'|'.$body.'|'.$timestamp;

        return hash_hmac('sha256', $stringToSign, $secret);
    }

    /**
     * Verify request signature
     */
    public static function verifyRequestSignature(
        string $method,
        string $uri,
        string $body,
        string $timestamp,
        string $signature,
        string $secret
    ): bool {
        $expectedSignature = self::generateRequestSignature($method, $uri, $body, $timestamp, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
