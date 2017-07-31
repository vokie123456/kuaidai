<?php

namespace App\Models;

use App\Providers\RouteServiceProvider;
use App\Services\PermissionImportService;

/**
 * @property int id
 * @property string node
 * @property string route
 */
class AdminNode extends \Eloquent
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['node', 'route'];

    /** 需要额外显示的字段 @var array */
    protected $appends = ['node_detail'];

    /**
     * 按路由控制器分组
     * @return array
     */
    public static function groupByCtlAll()
    {
        $nodes = self::all();
        $group = array();
        $exists = array();
        $namespace = app()->getProvider(RouteServiceProvider::class)->getNamespace();

        foreach ($nodes as $node) {
            list($ctl) = explode('@', $node->route);

            if (!isset($group[$ctl])) {
                $title = $ctl;
                if (class_exists($cls = "{$namespace}\\{$ctl}")) {
                    $title = self::getNodeTitle($cls) ? self::getNodeTitle($cls) : $ctl;
                }

                $group[$ctl] = array(
                    'title' => $title,
                    'actions' => []
                );
                $exists[$ctl] = array();
            }

            if (!isset($exists[$ctl][$node->id])) {
                $group[$ctl]['actions'][] = $node->toArray();
                $exists[$ctl][$node->id] = true;
            }
        }

        return $group;
    }

    /**
     * 获取控制器类的节点标量
     * @param $class
     * @return mixed
     */
    private static function getNodeTitle($class)
    {
        static $classHash = array();

        if (!isset($classHash[$class])) {
            $reflection = new \ReflectionClass($class);
            $docs = explode("\n", str_replace(["\r\n", "\r"], "\n", $reflection->getDocComment()));
            $title = '';

            foreach ($docs as $doc) {
                $doc = preg_replace("/\s(?=\s)/", "\\1", trim($doc, " \t\n\r\0\x0B*"));

                if (starts_with($doc, '@nodeTitle')) {
                    @list(, $title) = explode(' ', $doc);
                    break;
                }
            }

            $classHash[$class] = trim($title);
        }

        return $classHash[$class];
    }

    /**
     * 角色
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_permissions', 'node_id', 'role_id');
    }

    /**
     * 菜单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus()
    {
        return $this->hasMany(AdminMenu::class, 'node_id');
    }

    /**
     * 节点详情
     * @return string
     */
    public function getNodeDetailAttribute()
    {
        if (isset($this->attributes['node']) && isset($this->attributes['route'])) {
            $namespace = app()->getProvider(RouteServiceProvider::class)->getNamespace();
            @list($ctl) = explode('@', $this->attributes['route']);
            $ctl = "{$namespace}\\{$ctl}";

            if (class_exists($ctl)) {
                $nodeTitle = PermissionImportService::parseNode($ctl)['nodeTitle'];
                return "【{$nodeTitle}】{$this->attributes['node']}";
            } else {
                return $this->attributes['node'];
            }
        } else {
            return '';
        }
    }

}