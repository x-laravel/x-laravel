<?php

namespace App\Models\Log;

use Jenssegers\Mongodb\Eloquent\Model;

class HttpLog extends Model
{
    protected $connection = 'log';

    protected $fillable = [
        'method',
        'uri',
        'headers',
        'body',
        'response',
        'response_code',
        'ip_address',
        'elapsed_time',
    ];
}
