<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-19 22:11:32
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-06 22:53:38
 * @FilePath: /php/laravel/routes/deploy.php
 */

use App\Models\BookMarkSql;
use App\Models\LogSql;
use App\Models\MangaTagSql;
use App\Models\ScanSql;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

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

Route::any('test/ccc',function(){
    $inputImagePath = '/0docker/1localhost/poster/smanga_chapter_19665.png';  // 输入图片路径
    $inputImagePath = '/0docker/1localhost/test/10.jpg';  // 输入图片路径
    $outputImagePath = '/0docker/1localhost/poster/smanga_chapter_11.jpg';  // 输出压缩后的图片路径
    $targetFileSize = 300;  // 目标文件大小（KB）

    // // 使用 convert 命令压缩图片到指定大小
    // $command = "convert $inputImagePath -define jpeg:extent={$targetFileSize}KB $outputImagePath";
    // exec($command);

    // echo '图片已压缩完成。';

    // $targetFileSize = 300 * 1024;  // 目标文件大小（300 KB）

    // 获取输入图片的文件大小
    $fileSize = filesize($inputImagePath);

    if ($fileSize > $targetFileSize) {
        // 图片大小超过目标大小，进行压缩
        $command = "convert $inputImagePath -define jpeg:extent={$targetFileSize}KB $outputImagePath";
        exec($command);

        echo '图片已压缩完成。';
    } else {
        // 图片已经小于或等于目标大小，无需压缩
        echo '图片已经符合目标大小，无需压缩。';
    }


    // return Utils::compress_pictures('/0docker/1localhost/poster/smanga_chapter_1.png');
});

Route::any('test/c1',function(){
    $records = DB::table('compress')->where('updateTime','<',Carbon::now()->subDays(60))->get();
    foreach ($records as $key => $value) {
        echo $value->compressId;
        echo "<br>";
    }
    // dump($records);
});