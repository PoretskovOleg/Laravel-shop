<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Exceptions\TelegramBotApiException;
use Illuminate\Support\Facades\Http;
use Throwable;

final class TelegramBotApi
{
    const HOST = 'https://api.telegram.org/bot';

    public static function sendMessage(string $token, int $chatId, string $text): bool
    {
        try {
            $response = Http::get(self::HOST.$token.'/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ])->throw()->json();

            return $response['ok'] ?? false;
        } catch (Throwable $exception) {
            report(new TelegramBotApiException($exception->getMessage()));

            return false;
        }
    }
}
