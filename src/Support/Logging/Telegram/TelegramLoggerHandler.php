<?php

declare(strict_types=1);

namespace Support\Logging\Telegram;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;
use Services\Telegram\TelegramBotApiContract;

final class TelegramLoggerHandler extends AbstractProcessingHandler
{
    private string $token;

    private int $chatId;

    public function __construct(array $config)
    {
        $this->token = $config['token'];
        $this->chatId = intval($config['chat_id']);
        parent::__construct($config['level']);
    }

    protected function write(LogRecord $record): void
    {
        app(TelegramBotApiContract::class)::sendMessage(
            $this->token,
            $this->chatId,
            $record->formatted
        );
    }
}
