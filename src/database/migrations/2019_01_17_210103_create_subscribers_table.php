<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribersTable extends Migration
{
    private $tableName;

    public function __construct()
    {
        $this->tableName = (new \App\Subscriber())->getTable();
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('telegram_id');
            $table->unsignedInteger('chat_id')->nullable();
            $table->enum('type', [
                \App\Subscriber::TYPE_BOT,
                \App\Subscriber::TYPE_USER,
            ])->default(\App\Subscriber::TYPE_USER);
            $table->string('username');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('language_code')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
