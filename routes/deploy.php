<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-19 22:11:32
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-06 22:53:38
 * @FilePath: /php/laravel/routes/deploy.php
 */

use App\Models\BookMarkSql;
use App\Models\CompressSql;
use App\Models\LogSql;
use App\Models\MangaTagSql;
use App\Models\PathSql;
use App\Models\ScanSql;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use sqhlib\Hanzi\HanziConvert;
use Illuminate\Support\Facades\DB;

Route::post('deploy/database-test', [App\Http\Controllers\Deploy::class, 'database_test']);
Route::post('deploy/database-get', [App\Http\Controllers\Deploy::class, 'database_get']);
Route::post('deploy/database-set', [App\Http\Controllers\Deploy::class, 'database_set']);
Route::post('deploy/database-init', [App\Http\Controllers\Deploy::class, 'database_init']);

// 登录接口无验证
Route::any('user/login', [App\Http\Controllers\User::class, 'login']);

Route::any('info',  function () {
    return phpinfo();
});

Route::any('datatest', function () {
    dump(DB::table('version')->get());
});


Route::any('test/update-last-scan', function () {
    $res = PathSql::path_update_scan_time_now(2);
    dump($res);
});

Route::any('test/tr', function () {
    $str = "健身教練 1-104話+後記 [完結][無水印]";
    echo HanziConvert::convert($str);//默认是繁体转简体
    echo('<br>');
    echo HanziConvert::convert($str,true); //默认是繁体转简体
    echo ('<br>');
    echo HanziConvert::convert($str).'/'.HanziConvert::convert($str,true);
});