<?php

namespace App\Services;

class DokuSignatureService
{
    public static function generate(
        string $clientId,
        string $requestId,
        string $timestamp,
        string $requestBody,
        string $secretKey
    ): string {

        // Digest = Base64(SHA256(body))
        $digest = base64_encode(
            hash('sha256', $requestBody, true)
        );

        $stringToSign =
            "Client-Id:$clientId\n".
            "Request-Id:$requestId\n".
            "Request-Timestamp:$timestamp\n".
            "Request-Target:/checkout/v1/payment\n".
            "Digest:$digest";

        $signature = base64_encode(
            hash_hmac(
                'sha256',
                $stringToSign,
                $secretKey,
                true
            )
        );

        return "HMACSHA256=".$signature;
    }
}