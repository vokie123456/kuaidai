<?php

namespace App\Services;

use App\Components\Utils;
use App\Models\Admin;
use App\Models\AdminMenu;
use App\Providers\RouteServiceProvider;

class RbacService
{

    /** @var Admin */
    private $admin;

    /** @var string */
    private $guardName;

    /**
     * RbacService constructor.
     * @param $guardName
     */
    public function __construct($guardName)
    {
        $this->guardName = $guardName;
        $this->admin = \Auth::guard($guardName)->user();
    }

    /**
     * 生成SB Admin 2 风格菜单
     * @param null $menus
     * @param int $level
     * @return string
     */
    public function menuSBAdmin($menus = null, $level = 1)
    {
        if (is_null($menus)) {
            $menus = $this->cascadeMenu();
        }

        $html = '';
        $menuIcon = '';
        if ($level == 1) {
            $menuIcon = '<i class="glyphicon glyphicon-menu-hamburger"></i>';
            $navLevel = 'nav-second-level';
        } else {
            $navLevel = 'nav-third-level';
        }

        foreach ($menus as $menu) {
            $name = $menu['name'];

            $url = 'javascript:void(0);';
            if ($menu['route']) {
                try {
                    $url = action($menu['route']);
                } catch (\InvalidArgumentException $e) {
                }
            }
            if (empty($menu['child'])) {
                if ($url == 'javascript:void(0);') {
                    continue;
                }

                $html .= '<li><a href="' . $url . '">' . $menuIcon . ' ' . $name . '</a></li>';
            } else {
                $html .= '<li>
                        <a href="' . $url . '">' . $menuIcon . ' ' . $name . '<span class="fa arrow"></span></a>
                        <ul class="nav ' . $navLevel . '">
                            ' . $this->menuSBAdmin($menu['child'], $level + 1) . '
                        </ul>
                    </li>';
            }
        }

        return $html;
    }

    /**
     * 验证是否有权限访问
     * @param \Illuminate\Routing\Route $route
     * @return bool
     */
    public function check($route)
    {
        $permissions = array_column($this->getPermissions(), 'route');
        $namespace = app()->getProvider(RouteServiceProvider::class)->getNamespace() . '\\';
        $actionName = Utils::strReplaceLimit($namespace, '', $route->getActionName());

        if (in_array($actionName, $permissions)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 加载权限
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function loadPermissions()
    {
        static $roles = array();

        if (empty($roles)) {
            $roles = $this->admin->roles->load('permissions');
        }

        return $roles;
    }

    /**
     * 加载菜单
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function loadPermissionMenus()
    {
        static $menus = array();

        if (empty($menus)) {
            $menus = $this->loadPermissions()->load(array(
                'permissions.menus' => function($query) {
                    $query->from(\DB::raw('admin_menus force index (`node_id`)'));// 强制使用索引
                }
            ));
        }

        return $menus;
    }

    /**
     * 级联菜单
     * @param int $pid
     * @return array
     */
    private function cascadeMenu($pid = 0)
    {
        static $menuList = array();

        if (empty($menuList)) {
            $roles = $this->loadPermissionMenus();
            $permissions = array_column($roles->toArray(), 'permissions');
            $permissionMenus = array();
            foreach ($permissions as $permission) {
                $permissionMenus = array_merge($permissionMenus, array_column($permission, 'menus'));
            }

            foreach ($permissionMenus as $menus) {
                foreach ($menus as $menu) {
                    if (isset($menu['id'])) {
                        $menuList[$menu['id']] = $menu;
                    }
                }
            }

            $menuList = array_values($menuList);
            usort($menuList, function($a, $b) {
                return $a['weight'] != $b['weight'] ? $a['weight'] < $b['weight'] : $a['id'] > $b['id'];
            });
        }

        $result = array();

        foreach ($menuList as $menu) {
            if ($menu['pid'] == $pid) {

                $result[$menu['id']] = $menu;
                $result[$menu['id']]['child'] = $this->cascadeMenu($menu['id']);
            }
        }

        return $result;
    }

    /**
     * 获取权限
     * @return array|null
     */
    private function getPermissions()
    {
        static $permissions = array();

        if (empty($permissions)) {
            $roles = $this->loadPermissions()->toArray();
            foreach ($roles as $role) {
                foreach ($role['permissions'] as $permission) {
                    $permissions[] = $permission;
                }
            }
        }

        return $permissions;
    }

}