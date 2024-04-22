<?php

declare(strict_types=1);

namespace Services\Telegram;

use Illuminate\Support\Facades\Http;
use Services\Exceptions\TelegramBotApiException;
use Throwable;

class TelegramBotApi implements TelegramBotApiContract
{
    const HOST = 'https://api.telegram.org/bot';

    public static function fake(): TelegramBotApiFake
    {
        return app()->instance(
            TelegramBotApiContract::class,
            new TelegramBotApiFake()
        );
    }

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
