<?php

namespace App\Services;

class TelegramBotService
{
    const COMMAND_HELP = '/help';

    const COMMAND_SSL_INFO = 'ssl-info';

    const COMMAND_SUBSCRIBE = 'subscribe';

    private static $descriptions = [
        self::COMMAND_HELP => '/help - get information about available commands',
        self::COMMAND_SSL_INFO => 'ssl-info {domain} - get information about SSL certificate which domain used. Example: ssl-info www.example.com',
        self::COMMAND_SUBSCRIBE => 'subscribe - subscribe on the news from this bot',
    ];

    public static function getRules(): array
    {
        return [
            'domain' => 'required|regex:/^(https?:?\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)/'
        ];
    }

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