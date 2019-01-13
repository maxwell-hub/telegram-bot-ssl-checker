<?php

namespace App\Http\Controllers;

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

class TelegramBotController extends Controller
{
    /**
     * Precess telegram requests
     * @return \Symfony\Component\HttpFoundation\Response
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
        Log::info('Request:', [
            'data' => $data,
            'message' => $message
        ]);

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

                $cert = SslCertificate::createForHostName($domain);
                $textSslInfo = view('telegram_bot._ssl_info', ['cert' => $cert])->render();
                return $botMan->say($textSslInfo, $senderId)->send();
            } catch (BotManException $e) {
                Log::error(__METHOD__, [$e]);
            } catch (\Throwable $e) {
                Log::error(__METHOD__, [$e]);
            }
        }
        $botMan->hears(TelegramBotService::COMMAND_HELP, function (BotMan $botMan) {
            $botMan->reply(implode("\n", TelegramBotService::getCommandList()));
        });
        $botMan->listen();
        exit();
    }
}
