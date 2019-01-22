<?php

namespace App\Console\Commands;

use App\Subscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearSubscribers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear_subscribers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all subscribers with type "bot"';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::beginTransaction();
        Subscriber::where('type', Subscriber::TYPE_BOT)
            ->forceDelete();
        DB::commit();
    }
}
