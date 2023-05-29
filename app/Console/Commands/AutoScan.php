<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-28 17:32:59
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-05-29 23:57:10
 * @FilePath: /php/app/Console/Commands/AutoScan.php
 */

namespace App\Console\Commands;

use App\Jobs\Scan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoScan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:auto {dir?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dir = $this->argument('dir');

        $pathResults = DB::table('path')->whereRaw(" ? LIKE CONCAT('%', path, '%')", [$dir])->get();
        
        foreach ($pathResults as $val) {
            Scan::dispatch($val->pathId)->onQueue('scan');
        }

        return 0;
    }
}
