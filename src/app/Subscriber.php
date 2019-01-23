<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use SoftDeletes;

    const TYPE_BOT = 'bot';
    const TYPE_USER = 'user';

    /**
     * Table name
     *
     * @var string
     */
    protected $table = 'subscribers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'telegram_id',
        'chat_id',
        'username',
        'first_name',
        'last_name',
        'language_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
