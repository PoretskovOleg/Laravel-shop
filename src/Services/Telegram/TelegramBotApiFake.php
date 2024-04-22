<?php

declare(strict_types=1);

namespace Services\Telegram;

final class TelegramBotApiFake extends TelegramBotApi
{
    protected static bool $success = true;

    public function returnTrue(): TelegramBotApiFake
    {
        self::$success = true;

        return $this;
    }

    public function returnFalse(): TelegramBotApiFake
    {
        self::$success = false;

        return $this;
    }

    public static function sendMessage(string $token, int $chatId, string $text): bool
    {
        return self::$success;
    }
}
