<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\AdminNode;
use App\Models\AdminRole;
use App\Services\ImportService;
use DB;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "import {type : 导入数据类型，可选项：init-初始化项目（导入节点、菜单、超级角色、超级用户），node-节点，menu-菜单，clear-清理init所包含的所有项目。如：php artisan import menu。}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '项目数据导入';

    /** @var ImportService */
    private $service;

    /**
     * ImportCommand constructor.
     * @param ImportService $service
     */
    public function __construct(ImportService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'node':
                $this->importNode();
                break;

            case 'menu':
                $this->importMenu();
                break;

            case 'init':
                $this->initProject();
                break;

            case 'clear':
                $this->clear();
                break;

            default:
                $this->error("导入数据类型{$type}无效");
                break;
        }
    }

    /**
     * 清理项目
     */
    private function clear()
    {
        $this->info('准备清理');
        DB::beginTransaction();
        DB::statement('truncate table admin_groups');
        DB::statement('truncate table admin_menus');
        DB::statement('truncate table admin_nodes');
        DB::statement('truncate table admin_permissions');
        DB::statement('truncate table admin_roles');
        DB::statement('truncate table admins');
        DB::commit();
        $this->info('清理结束');
    }

    /**
     * 初始化项目
     */
    private function initProject()
    {
        // 验证超级用户是否存在
        if (Admin::where('id', 1)->exists()) {
            $this->error('超级用户已存在，无法初始化！');
            return;
        }

        // 开启事务
        DB::beginTransaction();

        try {
            // 导入节点
            $this->importNode();

            // 导入菜单
            $this->importMenu();

            // 创建超级角色
            $this->info('正在创建超级角色...');
            /** @var AdminRole $role */
            $role = AdminRole::create([
                'id' => 1,
                'role' => 'root',
                'name' => '超级角色',
            ]);
            $this->info('创建成功');

            $this->info('正在创建超级用户...');
            $password = str_random(8);
            $rootUsername = 'root';
            $admin = Admin::create([
                'id' => 1,
                'username' => $rootUsername,
                'password' => bcrypt($password),
                'name' => '超级用户',
            ]);
            $this->info('创建成功');

            $this->info('正在分配权限');
            // 分配用户角色
            $admin->roles()->attach([$role->id]);
            // 分配角色节点
            $nodes = AdminNode::select('id')->get()->toArray();
            $nodeIds = array_column($nodes, 'id');
            $role->permissions()->attach($nodeIds);

            // 提交事务
            DB::commit();

            $this->info('处理成功');
            $this->info("账号：{$rootUsername} 密码：{$password}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("出现异常，回滚事务。Exception {$e->getMessage()}");
        }
    }

    /**
     * 导入节点
     */
    private function importNode()
    {
        $this->info('正在导入节点...');
        $this->service->importNode();
        $this->info('导入成功');
    }

    /**
     * 导入菜单
     */
    private function importMenu()
    {
        $this->info('正在导入菜单...');
        $this->service->importMenu();
        $this->info('导入成功');
    }
}
