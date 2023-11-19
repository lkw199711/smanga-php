<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:46:35
* @FilePath: /smanga-php/app/Http/Version/Version349.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version349
{
    public function __construct()
    {
        // 生成任务队列表
        DB::statement("ALTER TABLE `lastRead` 
            ADD COLUMN `finish` int(1) ZEROFILL NOT NULL DEFAULT 0 COMMENT '已完成阅读' AFTER `page`;
        ");

        // 新增3.4.9版本记录
        VersionSql::add([
            'version' => '3.4.9',
            'versionDescribe' => '修复列表视图无法上下滚动的问题.',
            'createTime' => '2023-10-12 21:40:21'
        ]);
    }
}
