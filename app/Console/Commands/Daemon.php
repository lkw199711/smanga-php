<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-20 01:43:27
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-06-23 03:53:37
 * @FilePath: /php/laravel/app/Console/Commands/Daemon.php
 */

namespace App\Console\Commands;

use App\Http\Controllers\Deploy;
use App\Http\Controllers\Utils;
use App\Jobs\Scan;
use App\Models\ScanSql;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Daemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daemon:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daemon that remain in the background';

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
        // 设置脚本无限时间执行
        set_time_limit(0);

        $loopNum = 0;

        // 死循环进程
        while (true) {
            // 输出时间
            echo date("Y-m-d H:i:s");
            echo "\n\r";



            $configPath = getenv('SMANGA_CONFIG');
            $installLock = "$configPath/install.lock";
            $AppPath = getenv('SMANGA_APP');
            $versionFile = "$AppPath/version";

            // 未完成初次部署 等待
            if (!is_file($installLock)) {
                sleep(10);
                continue;
            }

            // 检查更新部署状态
            if (!is_file($versionFile)) {
                echo '执行更新部署\r\n';
                $ip = Utils::config_read('sql', 'ip');
                $userName = Utils::config_read('sql', 'userName');
                $passWord = Utils::config_read('sql', 'passWord');
                $database = Utils::config_read('sql', 'database');
                $port = Utils::config_read('sql', 'port');

                try {
                    $link = mysqli_connect($ip, $userName, $passWord, $database, $port);

                    // 执行初始化流程
                    Deploy::database_init(new Request());

                    $link->close();

                    echo "error sql connect success\r\n";
                } catch (\Exception $e) {
                    $msg = $e->getMessage();
                    echo "error sql connect failed $msg\r\n";
                }

                sleep(10);
            }

            // 清除扫描记录
            self::clear_scan();

            // 启动目录自动扫描 暂且放弃使用
            //self::auto_scan();

            // 定时扫描
            self::scheduled_scan();

            // 循环次数增加
            $loopNum++;

            // 睡眠一段时间
            sleep(60);
        }

        return 0;
    }

    /**
     * @description: 清除扫描记录
     * @return {*}
     */
    public function clear_scan()
    {
        $scanList = ScanSql::get_all();

        foreach ($scanList as $val) {
            if ($val->scanStatus === 'finish') {
                $clearTimeLimit = 0;
            } else {
                $clearTimeLimit = 1 * 60 * 60;
            }

            // 获取当前时间
            $currentDateTime = Carbon::now();

            // 假设有一个过去的时间点
            $previousDateTime = Carbon::parse($val->updateTime);

            // 计算时间差（以分钟为单位）
            $timeDifference = $currentDateTime->diffInSeconds($previousDateTime);
        
            dump($timeDifference);
            // 检查时间差是否超过十分钟（以秒为单位）
            if ($timeDifference > $clearTimeLimit) { // 十分钟等于 10 * 60 = 600 秒
                ScanSql::scan_delete($val->scanId);
            }
        }
    }

    /**
     * @description: 定时扫描任务
     * @return {*}
     */
    private static function scheduled_scan()
    {
        $pathArr = DB::table('path')->where('scheduledScan', 1)->get();

        $AppPath = getenv('SMANGA_APP');
        $configPath = getenv('SMANGA_CONFIG');
        $supervisorConfigPath = "$configPath/auto-scan";
        // 定时扫描时间间隔
        $interval = self::clac_interval();

        foreach ($pathArr as $val) {
            // 获取当前时间
            $currentDateTime = Carbon::now();

            // 假设有一个过去的时间点
            $previousDateTime = Carbon::parse($val->lastScanTime);

            // 计算时间差（以分钟为单位）
            $timeDifference = $currentDateTime->diffInSeconds($previousDateTime);

            // 检查时间差是否超过间隔（以秒为单位）
            if ($timeDifference > $interval) { // 十分钟等于 10 * 60 = 600 秒
                Scan::dispatch($val->pathId)->onQueue('scan');
            }
        }
    }

    /**
     * @description: 自动扫描
     * @return {*}
     */
    public function auto_scan()
    {
        $pathArr = DB::table('path')->where('autoScan', 1)->get();

        $AppPath = getenv('SMANGA_APP');
        $configPath = getenv('SMANGA_CONFIG');
        $supervisorConfigPath = "$configPath/auto-scan";
        // echo $AppPath;exit;
        foreach ($pathArr as $val) {
            $path = $val->path;
            $md5 = md5($val->path);
            $configFileName = "{$supervisorConfigPath}/$md5.ini";

            $configTitle = "program:path-$md5";
            if (!is_file($configFileName)) {

                $command = "$AppPath/monitor.sh '$path'";

                Utils::supervisor_write($configFileName, $configTitle, [
                    'process_name' => '%(program_name)s_%(process_num)02d',
                    'command' => $command,
                    'autostart' => 'true',
                    'autorestart' => 'true',
                    'redirect_stderr' => 'true',
                    'user' => 'smanga',
                    'directory' => $AppPath,
                    'logfile' => "{$supervisorConfigPath}/$md5.log",
                    'stdout_logfile' => "{$supervisorConfigPath}/$md5.log"
                ]);

                dump(exec('supervisorctl reread'));
                exec('supervisorctl update');

                dump('未找到命令');
            } else {
                dump('已找到命令');
            }
        }
    }

    /**
     * @description: 当参数带有*时, 计算其结果
     * @return {*}
     */
    private static function clac_interval()
    {
        $interval  = Utils::config_read('scan', 'interval');
        $result = 1;
        if (strpos($interval, '*')) {
            $intervalArr = explode('*', $interval);
            foreach ($intervalArr as $val) {
                $result = $result * (int)$val;
            }

            $interval = $result;
        }

        return $interval;
    }
}
