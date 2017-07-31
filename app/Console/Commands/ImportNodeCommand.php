<?php

namespace App\Console\Commands;

use App\Services\PermissionImportService;
use Illuminate\Console\Command;

class ImportNodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-node';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '导入并自动更新后台节点';

    /**
     * Execute the console command.
     * @param PermissionImportService $service
     */
    public function handle(PermissionImportService $service)
    {
        $service->importNode();
        $this->info('导入成功');
    }
}
