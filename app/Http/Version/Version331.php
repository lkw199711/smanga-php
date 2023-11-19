<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:29:34
* @FilePath: /smanga-php/app/Http/Version/Version331.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version331
{
    public function __construct()
    {
        // 创建长连接表
        DB::statement("CREATE TABLE IF NOT EXISTS `socket` (
            `socketId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
            `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
            `fd` int(11) NOT NULL,
            `userId` int(11) NULL DEFAULT NULL,
            `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
            `createTime` datetime(0) NULL DEFAULT NULL,
            `updateTime` datetime(0) NULL DEFAULT NULL,
            PRIMARY KEY (`socketId`, `fd`) USING BTREE
            ) ENGINE = MEMORY AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        // 创建消息表
        DB::statement("CREATE TABLE IF NOT EXISTS `notice` (
            `noticeId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `userId` int(11) NULL DEFAULT NULL,
            `code` int(1) NULL DEFAULT NULL,
            `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
            `createTime` datetime(0) NULL DEFAULT NULL,
            `updateTime` datetime(0) NULL DEFAULT NULL,
            PRIMARY KEY (`noticeId`) USING BTREE
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        // 新增自动扫描字段
        DB::statement("ALTER TABLE `path` 
            ADD COLUMN `autoScan` int(1) ZEROFILL NULL COMMENT '自动扫描' AFTER `path`,
            ADD COLUMN `include` varchar(255) NULL COMMENT '包含匹配' AFTER `autoScan`,
            ADD COLUMN `exclude` varchar(255) NULL COMMENT '排除匹配' AFTER `include`,
            ADD COLUMN `updateTime` datetime(0) NULL COMMENT '更新时间' AFTER `createTime`;
        ");

        // 新增3.3.1版本记录
        VersionSql::add([
            'version' => '3.3.1',
            'versionDescribe' => '使用websocket进行消息通知.',
            'createTime' => '2023-5-22 21:05:00'
        ]);
    }
}
