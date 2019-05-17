<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobZhiLian extends Model
{
    protected $table = 'zhilian';

    protected $fillable = [
        'job_position', 'salary', 'summary', 'job_desc', 'job_address', 'updated_time',
    ];

    protected $dates = [];

    protected $casts = [];

    protected $appends = [];

    public $timestamps = false;

    public function toESArray()
    {
        return array_only($this->toArray(), ['id', 'salary', 'summary', 'job_desc', 'job_address']);
    }
}
