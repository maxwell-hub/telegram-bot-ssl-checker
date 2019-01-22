<?php

namespace App\Console\Commands;

use Faker\Factory;
use App\Subscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PopulateSubscribers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate_subscribers {--count=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate subscribers table users with type "bot"';

    private $maxCount = 100;

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
        $infoName = 'Command ' . $this->getName();
        $this->info("$infoName has been started");
        $faker = Factory::create();
        $count = (int)$this->option('count');
        if ($count && $count > $this->maxCount) {
            $count = $this->maxCount;
        }
        try {
            DB::beginTransaction();
            $i = 0;
            while ($i < $count) {
                $subscriber = new Subscriber();
                $subscriber->type = Subscriber::TYPE_BOT;
                $subscriber->fill([
                    'telegram_id' => random_int(1000, 100000),
                    'chat_id' => random_int(1000, 100000),
                    'username' => $faker->userName,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'language_code' => 'en',
                ])->save();
                $i++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Command ' . $this->getName(), [$e->getMessage()]);
        }
        $this->info("$infoName has been finished");
    }
}
