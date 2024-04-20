<?php

namespace Tests\Unit\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Services\Telegram\TelegramBotApi;
use Tests\TestCase;

class TelegramBotApiTest extends TestCase
{
    public function test_send_message_success(): void
    {
        Http::fake([
            TelegramBotApi::HOST.'*' => Http::response(['ok' => true])
        ]);

        $result = TelegramBotApi::sendMessage('token', 1, 'Test message');

        $this->assertTrue($result);
    }

    public function test_send_message_error(): void
    {
        Http::fake([
            TelegramBotApi::HOST.'*' => Http::response()
        ]);

        $result = TelegramBotApi::sendMessage('token', 1, 'Test message');

        $this->assertFalse($result);
    }
}
