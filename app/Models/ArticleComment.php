<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int status
 */
class ArticleComment extends \Eloquent
{
    use SoftDeletes;

    /** 状态-正常 */
    const STATUS_NORMAL = 1;

    /** 状态-禁用 */
    const STATUS_DENY = 2;

    protected $fillable = [

    ];

    protected $appends = [
        'is_deleted', 'status_text'
    ];

    /**
     * 关联用户
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 关联文章
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 是否删除
     * @return bool
     */
    public function getIsDeletedAttribute()
    {
        if (empty($this->attributes['deleted_at'])) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 状态文本
     * @return string
     */
    public function getStatusTextAttribute()
    {
        if (isset($this->attributes['status']) && $this->attributes['status'] == self::STATUS_NORMAL) {
            return '正常';
        } else {
            return '禁用';
        }
    }
}
