<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int id
 * @property string username
 * @property string nickname
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * 通过username查找用户，若用户不存在，则创建该用户
     * @param $username
     * @param $column
     * @return User
     */
    public static function findByUsernameOrCreate($username, $column)
    {
        $user = self::where('username', $username)->first();

        if (empty($user)) {
            $user = self::create($column);
        }

        return $user;
    }
}
