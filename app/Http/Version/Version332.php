<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:31:49
* @FilePath: /smanga-php/app/Http/Version/Version332.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version332
{
    public function __construct()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `log`  (
            `logId` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,
            `logType` varchar(255) NOT NULL DEFAULT 'process' COMMENT '日志类型 error/process/operate',
            `logLevel` int(0) NULL DEFAULT 0 COMMENT '日志等级',
            `logContent` varchar(255) NULL COMMENT '日志内容',
            `createTime` datetime(0) NULL,
            `updateTime` datetime(0) NULL,
            PRIMARY KEY (`logId`)
        );");

        // 新增3.3.2版本记录
        VersionSql::add([
            'version' => '3.3.2',
            'versionDescribe' => '新增日志模块',
            'createTime' => '2023-6-12 22:16:00'
        ]);
    }
}
