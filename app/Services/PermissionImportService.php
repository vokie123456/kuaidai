<?php

namespace App\Services;

use App\Models\AdminMenu;
use App\Models\AdminNode;
use App\Providers\RouteServiceProvider;
use ReflectionClass;

/**
 * 后台权限自动导入服务
 * @see PermissionImportService::importNode() 导入节点
 * @see PermissionImportService::importMenu() 导入菜单
 */
class PermissionImportService
{

    /**
     * 导入菜单
     */
    public function importMenu()
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = null;
        $menus = [];
        $repeatCtls = [];
        $namespace = app()->getProvider(RouteServiceProvider::class)->getNamespace();

        foreach (\Route::getRoutes()->getRoutes() as $route) {
            $action = $route->getAction();
            if (!isset($action['controller'])) {
                continue;
            }

            @list($ctl, $act) = explode('@', $action['controller']);
            if (!($ctl && $act) || !class_exists($ctl)) {
                continue;
            }

            if (isset($repeatCtls[$ctl])) {
                continue;
            }

            //通过反射获取类的注释
            $nodeInfoHash = self::parseNode($ctl);
            if (is_array($nodeInfoHash['menu'])) {
                $menus[substr($ctl, strlen($namespace) + 1, strlen($ctl)) . '@' . $nodeInfoHash['menu']['route']] = $nodeInfoHash['menu']['name'];
            }
        }

        /** @var AdminMenu $menu */
        $menu = null;
        foreach (AdminMenu::get() as $menu) {
            if (isset($menus[$menu->route])) {
                $menu->name = $menus[$menu->route];
                $menu->saveOrFail();
                unset($menus[$menu->route]);
            }
        }

        if (count($menus) > 0) {
            $newMenus = array();
            foreach ($menus as $route => $name) {
                $newMenus[] = array(
                    'pid' => 0,
                    'route' => $route,
                    'name' => $name,
                );
            }

            AdminMenu::insert($newMenus);
        }
    }

    /**
     * 导入节点
     */
    public function importNode()
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = null;
        $nodes = [];
        $repeatCtls = [];
        $namespace = app()->getProvider(RouteServiceProvider::class)->getNamespace();

        // 判断是否有公共节点
        if (!AdminNode::find(1)) {
            AdminNode::create([
                'id' => 1,
                'node' => '公共',
                'route' => '公共',
            ]);
        }

        foreach (\Route::getRoutes()->getRoutes() as $route) {
            $action = $route->getAction();
            if (!isset($action['controller'])) {
                continue;
            }

            @list($ctl, $act) = explode('@', $action['controller']);
            if (!($ctl && $act) || !class_exists($ctl)) {
                continue;
            }

            if (isset($repeatCtls[$ctl])) {
                continue;
            }

            //通过反射获取类的注释
            $nodeInfoHash = self::parseNode($ctl);
            foreach ($nodeInfoHash['nodes'] as $node) {
                $nodes[substr($ctl, strlen($namespace) + 1, strlen($ctl)) . '@' . $node['route']] = $node['node'];
            }
        }

        /** @var AdminNode $node */
        $node = null;
        foreach (AdminNode::get() as $node) {
            if (isset($nodes[$node->route])) {
                $node->node = $nodes[$node->route];
                $node->saveOrFail();
                unset($nodes[$node->route]);
            }
        }

        if (count($nodes) > 0) {
            $newNodes = array();
            foreach ($nodes as $route => $node) {
                $newNodes[] = array(
                    'route' => $route,
                    'node' => $node
                );
            }

            AdminNode::insert($newNodes);
        }
    }

    /**
     * 解析节点信息
     * @param $class
     * @return array
     *  array(
     *      'nodeTitle' => '节点控制器名称',
     *      'menu' => array('route' => '路由', 'name' => '菜单名称'),// 如果有配置菜单，返回关联数组，否则返回NULL
     *      'nodes' => array(
     *          array('route' => '路由，如：index', 'node' => '显示名称，如：列表'),
     *          array('route' => '路由，如：store', 'node' => '显示名称，如：保存'),
     *          array('route' => '路由，如：update', 'node' => '显示名称，如：更新'),
     *      ),
     *  )
     */
    public static function parseNode($class)
    {
        static $classMap = array();

        if (!isset($classMap[$class])) {
            $reflection = new ReflectionClass($class);
            $docs = explode("\n", str_replace(["\r\n", "\r"], "\n", $reflection->getDocComment()));
            $nodeHash = array(
                'nodeTitle' => null,
                'menu' => null,
                'nodes' => []
            );

            foreach ($docs as $doc) {
                $doc = preg_replace("/\s(?=\s)/", "\\1", trim($doc, " \t\n\r\0\x0B*"));

                if (starts_with($doc, '@nodeTitle')) {
                    @list(, $nodeHash['nodeTitle']) = explode(' ', $doc);
                } elseif (starts_with($doc, '@nodeName')) {
                    @list(, $method, $nodeName) = explode(' ', $doc);

                    if (!($method && $nodeName) || !$reflection->hasMethod($method)) {
                        continue;
                    }

                    $nodeHash['nodes'][] = array(
                        'route' => $method,
                        'node' => $nodeName
                    );
                } elseif (starts_with($doc, '@menu')) {
                    @list(, $method, $menuName) = explode(' ', $doc);
                    if (!($method && $menuName) || !$reflection->hasMethod($method)) {
                        continue;
                    }

                    $nodeHash['menu'] = array(
                        'route' => $method,
                        'name' => $menuName
                    );
                }
            }

            $classMap[$class] = $nodeHash;
        }

        return $classMap[$class];
    }

}