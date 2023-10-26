<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-10-23 14:38:42
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-26 14:19:29
 * @FilePath: /smanga-php/app/Http/Controllers/JobDispatch.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace App\Http\Controllers;

use App\Jobs\Compress;
use App\Jobs\DeleteManga;
use App\Jobs\DeleteMedia;
use App\Jobs\DeletePath;
use App\Jobs\Scan;
use App\Jobs\ScanManga;
use Illuminate\Http\Request;

class JobDispatch extends Controller
{
    //
    public static function handle($jobName, $queueName, ...$jobData)
    {
        $dispatchSync = Utils::config_read('debug', 'dispatchSync');
        $dispatchMethod = $dispatchSync ? 'dispatchSync' : 'dispatch';
        $onQueue =  '';

        switch ($jobName) {
            case 'DeletePath':
                # code...
                if ($dispatchSync) {
                    DeletePath::dispatchSync(...$jobData);
                } else {
                    DeletePath::dispatch(...$jobData)->onQueue($queueName);
                }
                break;
            case 'DeleteManga':
                # code...
                if ($dispatchSync) {
                    DeleteManga::dispatchSync(...$jobData);
                } else {
                    DeleteManga::dispatch(...$jobData)->onQueue($queueName);
                }
                break;
            case 'DeleteMedia':
                # code...
                if ($dispatchSync) {
                    DeleteMedia::dispatchSync(...$jobData);
                } else {
                    DeleteMedia::dispatch(...$jobData)->onQueue($queueName);
                }
                break;
            case 'Scan':
                # code...
                if ($dispatchSync) {
                    Scan::dispatchSync(...$jobData);
                } else {
                    Scan::dispatch(...$jobData)->onQueue($queueName);
                }
                break;
            case 'ScanManga':
                # code...
                if ($dispatchSync) {
                    ScanManga::dispatchSync(...$jobData);
                } else {
                    ScanManga::dispatch(...$jobData)->onQueue($queueName);
                }
                break;
        }
    }
}
