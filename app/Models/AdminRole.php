<?php

namespace App\Models;

use Eloquent;

/**
 * @property int id
 */
class AdminRole extends Eloquent
{

    protected $fillable = ['id', 'role', 'name'];

    /**
     * 权限组
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(AdminNode::class, 'admin_permissions', 'role_id', 'node_id');
    }

    /**
     * 用户组
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_groups', 'role_id', 'admin_id');
    }
}