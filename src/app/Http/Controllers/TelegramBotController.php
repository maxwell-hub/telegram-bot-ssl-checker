<?php

namespace App\Http\Controllers;

use App\Classes\Helpers;
use Illuminate\Http\Request;
use BotMan\BotMan\BotManFactory;
use Illuminate\Support\Facades\Log;
use App\Services\ValidationService;
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
        $message = array_get($data, 'text', '');
        $senderId = array_get($data, 'from.id', '');
        Log::info('Request:', [
            'data' => $data,
            'message' => $message
        ]);

        if (strpos($message, 'ssl-info') !== false) {
            try {
                $messageParams = explode(' ', $message);
                $domain = array_get($messageParams, 1, '');
                $domain = Helpers::extractDomain($domain);
                $validatorService = new ValidationService();

                $validatorService->validate([
                    'domain' => $domain
                ], [
                    'domain' => 'required|url'
                ], [
                    'url' => __('bot.domain_invalid'),
                    'required' => __('bot.domain_required')
                ]);
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
        exit();
    }
}
