<?php

namespace App\Console\Commands;

use App\Services\PermissionImportService;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {type : 导入数据类型，可选项：node-节点，menu-菜单。如：php artisan import menu。}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '项目数据导入';

    /** @var PermissionImportService */
    private $service;

    /**
     * ImportCommand constructor.
     * @param PermissionImportService $service
     */
    public function __construct(PermissionImportService $service)
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

            default:
                $this->error("导入数据类型{$type}无效");
                break;
        }
    }

    /**
     * 导入节点
     */
    private function importNode()
    {
        $this->info('正在导入...');
        $this->service->importNode();
        $this->info('导入成功');
    }

    /**
     * 导入菜单
     */
    private function importMenu()
    {
        $this->info('正在导入...');
        $this->service->importMenu();
        $this->info('导入成功');
    }
}
