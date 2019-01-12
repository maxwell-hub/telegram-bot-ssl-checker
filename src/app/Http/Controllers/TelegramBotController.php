<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;

class TelegramBotController extends Controller
{
    public function __invoke()
    {
        $config = [
            'telegram' => [
                'token' => env('TELEGRAM_TOKEN')
            ]
        ];

        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);

        $botMan = BotManFactory::create($config);

        $botMan->hears('/hello', function (BotMan $bot) {
            $bot->reply('Hello yourself.');
        });
        $botMan->hears('/hi', function (BotMan $bot) {
            $bot->reply('hi yourself.');
        });

        $botMan->listen();
    }
}
