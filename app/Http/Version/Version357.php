<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:52:28
* @FilePath: /smanga-php/app/Http/Version/Version357.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version357
{
    public function __construct()
    {
        // 生成日志表
        DB::statement("ALTER TABLE `log` 
            ADD COLUMN `errorMessage` text NULL COMMENT '代码错误日志' AFTER `updateTime`;
        ");

        VersionSql::add([
            'version' => '3.5.7',
            'versionDescribe' => '优化代码,增加图片错误处理.',
            'createTime' => '2023-10-26 21:58:16'
        ]);
    }
}
