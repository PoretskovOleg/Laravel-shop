<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;

final class TelegramBotApi
{
    const TELEGRAM_API_HOST = 'https://api.telegram.org/bot';

    public static function sendMessage(string $token, int $chatId, string $text): void
    {
        Http::get(self::TELEGRAM_API_HOST.$token.'/sendMessage', [
            'chat_id' => $chatId,
            'text' => $text,
        ]);
    }
}
