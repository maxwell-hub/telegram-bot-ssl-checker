<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Services\TelegramBotService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $subscribersIds;

    private $message;

    /**
     * Create a new job instance.
     * SendTelegramMessage constructor.
     *
     * @param array $subscribersIds
     * @param string $message
     */
    public function __construct(array $subscribersIds, string $message)
    {
        $this->message = $message;
        $this->subscribersIds = $subscribersIds;
    }

    /**
     * Execute the job.
     *
     * @param TelegramBotService $botService
     * @return void
     */
    public function handle(TelegramBotService $botService)
    {
        $botService->send($this->subscribersIds, $this->message);
    }
}
