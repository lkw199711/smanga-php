<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-19 22:11:32
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-02 21:42:42
 * @FilePath: /php/laravel/routes/deploy.php
 */

use App\Models\BookMarkSql;
use App\Models\LogSql;
use App\Models\MangaTagSql;
use App\Models\ScanSql;
use Illuminate\Support\Facades\Route;

Route::post('deploy/database-test', [App\Http\Controllers\Deploy::class, 'database_test']);
Route::post('deploy/database-get', [App\Http\Controllers\Deploy::class, 'database_get']);
Route::post('deploy/database-set', [App\Http\Controllers\Deploy::class, 'database_set']);
Route::post('deploy/database-init', [App\Http\Controllers\Deploy::class, 'database_init']);

// 登录接口无验证
Route::any('user/login', [App\Http\Controllers\User::class, 'login']);

Route::any('info',  function () {
    return phpinfo();
});

use Illuminate\Support\Facades\DB;

Route::any('datatest', function () {
    dump(DB::table('version')->get());
});

Route::any('test/get', [App\Http\Controllers\Test::class, 'get']);
Route::any('test', [App\Http\Controllers\Test::class, 'test']);

Route::get('/', function () {
    return view('welcome');
});

Route::any('test/log', function () {
    // 新增一条日志
    LogSql::add([
        // 'logType' => 'process',
        // 'logLevel' => 5,
        'logContent' => '这是一条测试日志，查看默认值设置是否生效'
    ]);

    return '日志添加成功';
});

Route::any('test/yu', function () {
    $data = ['mangaName' => '漫画名称'];
    echo "漫画 '{$data['mangaName']}' 插入失败。";
    dump("漫画 '{$data['mangaName']}' 插入失败。");

    return '日志添加成功';
});



Route::any('test/scan', function () {
    $path = '/mnt/single0/single0/18manga/00日漫/出包王女';
    $scanDir = scandir($path);
    $scanDir = array_diff($scanDir, ['.', '..']);
    $scanDir = array_map(fn ($n) => $path . '/' . $n, $scanDir);
    dump($scanDir);

    return;
    // 新增一条日志
    return ScanSql::add([
        'scanStatus' => 'start',
        'path' => '/123/345',
        'pathId' => '111',
        'targetPath' => '456/234453',
        'scanCount' => 1000,
        'scanIndex' => 8
    ]);

    // return '日志扫描记录成功';
});

Route::any('test/iiii', [App\Http\Controllers\Deploy::class, 'set_ssl']);