<?php

namespace App\Http\Controllers;

use App\Subscriber;
use App\Classes\Helpers;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\BotManFactory;
use Illuminate\Support\Facades\Log;
use App\Services\ValidationService;
use App\Services\TelegramBotService;
use BotMan\BotMan\Drivers\DriverManager;
use Spatie\SslCertificate\SslCertificate;
use BotMan\BotMan\Exceptions\Base\BotManException;
use Spatie\SslCertificate\Exceptions\CouldNotDownloadCertificate;

class TelegramBotController extends Controller
{
    /**
     * Precess telegram requests
     */
    public function run()
    {
        DriverManager::loadDriver(\BotMan\Drivers\Telegram\TelegramDriver::class);
        $config['telegram'] = config('app.telegram');

        /** @var Request $request */
        $request = app(Request::class);
        $botMan = BotManFactory::create($config);
        $data = $request->get('message');
        $message = trim(array_get($data, 'text', ''));
        $senderId = array_get($data, 'from.id', '');
        Log::info('Request:', ['data' => $data]);

        if (strpos($message, TelegramBotService::COMMAND_SSL_INFO) !== false) {
            try {
                $domain = trim(str_replace_first(TelegramBotService::COMMAND_SSL_INFO, '', $message));
                $domain = Helpers::extractDomain($domain);

                $validatorService = new ValidationService();
                $validatorService->validate([
                    'domain' => $domain
                ],
                    TelegramBotService::getRules(),
                    TelegramBotService::getValidationMessages()
                );
                if ($validatorService->fails()) {
                    Log::error('Validation errors', $validatorService->getMessages());
                    return $botMan->say($validatorService->getErrorsAsString(), $senderId)->send();
                }

                try {
                    $cert = SslCertificate::createForHostName($domain);
                    $textSslInfo = view('telegram_bot._ssl_info', ['cert' => $cert])->render();
                } catch (CouldNotDownloadCertificate $e) {
                    $textSslInfo = $e->getMessage();
                }
                return $botMan->say($textSslInfo, $senderId)->send();
            } catch (BotManException $e) {
                Log::error(__METHOD__, [$e]);
            } catch (\Throwable $e) {
                Log::error(__METHOD__, [$e]);
            }
        } elseif ($message == TelegramBotService::COMMAND_SUBSCRIBE) {
            Subscriber::create([
                'telegram_id' => $senderId,
                'chat_id' => $request->get('chat.chat_id'),
                'username' => $request->get('from.username'),
                'first_name' => $request->get('from.first_name'),
                'last_name' => $request->get('from.last_name'),
                'language_code' => $request->get('from.language_code'),
            ]);
            $botMan->hears(TelegramBotService::COMMAND_SUBSCRIBE, function (BotMan $botMan) {
                $botMan->reply(__('bot.success_subscribed'));
            });
        } elseif ($message == TelegramBotService::COMMAND_SUBSCRIBE) {
            Subscriber::whereTelegramId($senderId)
                ->detete();
            $botMan->hears(TelegramBotService::COMMAND_SUBSCRIBE, function (BotMan $botMan) {
                $botMan->reply(__('bot.success_unsubscribed'));
            });
        } elseif ($message == TelegramBotService::COMMAND_HELP) {
            $botMan->hears(TelegramBotService::COMMAND_HELP, function (BotMan $botMan) {
                $botMan->reply(implode("\n", TelegramBotService::getCommandList()));
            });
        } else {
            try {
                return $botMan->say(__('bot.command_not_found'), $senderId);
            } catch (\Exception $e) {
                Log::error(__METHOD__, [$e->getMessage(), $data]);
            }
        }
        exit();
    }
}
