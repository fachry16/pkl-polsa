<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendPerpusNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    public function __construct(
        public array $payload,
    ) {}

    public function handle(): void
    {
        $webhookUrl = config('services.perpus.webhook_url');

        if (empty($webhookUrl)) {
            return;
        }

        $secret = (string) config('services.perpus.webhook_secret');
        $request = Http::timeout(5);

        if ($secret !== '') {
            $signature = hash_hmac('sha256', json_encode($this->payload, JSON_THROW_ON_ERROR), $secret);
            $request = $request->withHeaders([
                'X-Eduva-Signature' => $signature,
                'X-Eduva-Secret' => $secret,
            ]);
        }

        $response = $request->post($webhookUrl, $this->payload);

        if ($response->failed()) {
            Log::warning('Webhook notifikasi ke Perpus gagal dengan status: '.$response->status(), [
                'nim' => $this->payload['nim'] ?? null,
                'type' => $this->payload['type'] ?? null,
                'body' => $response->body(),
            ]);
        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Job kirim webhook notifikasi LMS ke Perpus gagal permanen', [
            'nim' => $this->payload['nim'] ?? null,
            'type' => $this->payload['type'] ?? null,
            'error' => $exception?->getMessage(),
        ]);
    }
}
