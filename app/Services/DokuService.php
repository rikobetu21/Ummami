<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Services\DokuSignatureService;

class DokuService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('doku.base_url');
        $this->clientId = config('doku.client_id');
        $this->secretKey = config('doku.secret_key');
    }

    public function getConfig()
    {
        return [
            'base_url' => $this->baseUrl,
            'client_id' => $this->clientId,
            'secret_key' => $this->secretKey
                ? substr($this->secretKey, 0, 8) . '********'
                : null,
        ];
    }

    public function createCheckout(int $amount, string $invoice)
    {
        $requestId = (string) Str::uuid();

        $timestamp = Carbon::now('UTC')
            ->format('Y-m-d\TH:i:s\Z');

        $body = [

            'order' => [

                'amount' => $amount,

                'invoice_number' => $invoice,

                // setelah pembayaran selesai browser diarahkan ke sini
                'callback_url_result' => config('doku.return_url'),

                'auto_redirect' => true,

            ],

            'payment' => [

                'payment_due_date' => 60,

            ],

            // webhook DOKU
            'additional_info' => [

                'override_notification_url' => config('doku.callback_url'),

            ],

        ];

        $json = json_encode($body);

        $signature = DokuSignatureService::generate(

            $this->clientId,

            $requestId,

            $timestamp,

            $json,

            $this->secretKey

        );

        $response = Http::withHeaders([

            'Client-Id' => $this->clientId,

            'Request-Id' => $requestId,

            'Request-Timestamp' => $timestamp,

            'Signature' => $signature,

            'Content-Type' => 'application/json',

        ])->post(
                $this->baseUrl . '/checkout/v1/payment',
                $body
            );

        return $response->json();
    }
}