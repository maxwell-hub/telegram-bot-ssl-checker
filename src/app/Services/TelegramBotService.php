<?php

namespace App\Services;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    const COMMAND_HELP = '/help';

    const COMMAND_SSL_INFO = 'ssl-info';

    const COMMAND_SUBSCRIBE = 'subscribe';

    const COMMAND_UNSUBSCRIBE = 'unsubscribe';

    private static $descriptions = [
        self::COMMAND_HELP => '/help - get information about available commands',
        self::COMMAND_SSL_INFO => 'ssl-info {domain} - get information about SSL certificate which domain used. Example: ssl-info www.example.com',
        self::COMMAND_SUBSCRIBE => 'subscribe - subscribe on the news from this bot',
        self::COMMAND_UNSUBSCRIBE => 'unsubscribe - unsubscribe from this bot',
    ];

    /**
     * Send messages to telegram subscribers
     * @param array $subscribersIds
     * @param string $message
     */
    public function send(array $subscribersIds, string $message)
    {
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
        $config['telegram'] = config('app.telegram');
        $botMan = BotManFactory::create($config);
        try {
            Log::info(__METHOD__ . ' Message sent', ['subscribersIds' => $subscribersIds, 'message' => $message]);
            $botMan->say($message, $subscribersIds, TelegramDriver::class);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                $e->getMessage(),
                $message,
                $subscribersIds
            ]);
        }
    }

    /**
     * Returns validation rules
     * @return array
     */
    public static function getRules(): array
    {
        return [
            'domain' => 'required|regex:/^(https?:?\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)/'
        ];
    }

    /**
     * Returns validation messages
     * @return array
     */
    public static function getValidationMessages(): array
    {
        return [
            'regex' => __('bot.domain_invalid'),
            'required' => __('bot.domain_required')
        ];
    }

    /**
     * Returns descriptions about used commands
     * @return array
     */
    public static function getDescriptions(): array
    {
        return self::$descriptions;
    }

    /**
     * Returns command list
     * @return array
     */
    public static function getCommandList(): array
    {
        return array_values(self::getDescriptions());
    }
}