<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-19 22:11:32
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-06-13 01:50:59
 * @FilePath: /php/laravel/routes/deploy.php
 */

use App\Models\LogSql;
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
    $aaa = '123';
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
        'logType' => 'process',
        'logLevel' => 2,
        'logContent' => '这是一条流程日志,他是在系统操作过程中生成的,表示系统正在工作的流程'
    ]);

    return '日志添加成功';
});
