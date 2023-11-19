<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:32:04
* @FilePath: /smanga-php/app/Http/Version/Version333.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version333
{
    public function __construct()
    {

        DB::statement("CREATE TABLE IF NOT EXISTS `scan`  (
            `scanId` int(0) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
            `scanStatus` varchar(255) NULL COMMENT 'start|scaning|finish',
            `path` varchar(255) NULL COMMENT '路径',
            `targetPath` varchar(255) NULL COMMENT '正在扫描的二级路径',
            `pathId` int(0) NOT NULL COMMENT '第二主键',
            `scanCount` int(0) NULL COMMENT '扫描目标总数',
            `scanIndex` int(0) NULL COMMENT '正在扫描的进度',
            `createTime` datetime(0) NULL COMMENT '扫描任务开始时间',
            `updateTime` datetime(0) NULL COMMENT '扫描任务更新时间',
            PRIMARY KEY (`scanId`, `pathId`)
        ) ENGINE = MEMORY;");

        // 新增定时扫描字段
        DB::statement("ALTER TABLE `path` 
            ADD COLUMN `scheduledScan` int(1) ZEROFILL NULL COMMENT '定时扫描' AFTER `autoScan`,
            ADD COLUMN `lastScanTime` datetime(0) NULL COMMENT '上次扫描时间' AFTER `exclude`;
        ");

        // 新增3.3.3版本记录
        VersionSql::add([
            'version' => '3.3.3',
            'versionDescribe' => '扫描系统做节流处理,正在进行扫描的目录不再接收扫描任务',
            'createTime' => '2023-6-13 20:52:00'
        ]);
    }
}
