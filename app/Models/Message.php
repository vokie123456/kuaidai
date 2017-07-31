<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends \Eloquent
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ip', 'content'
    ];

    /**
     * 关联用户
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}