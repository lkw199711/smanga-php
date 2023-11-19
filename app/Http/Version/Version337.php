<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:34:27
* @FilePath: /smanga-php/app/Http/Version/Version337.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version337
{
    public function __construct()
    {
        // 创建标签表
        DB::statement("CREATE TABLE IF NOT EXISTS `tag`  (
            `tagId` int(0) NOT NULL AUTO_INCREMENT COMMENT '标签主键',
            `tagName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签名称',
            `tagColor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签颜色',
            `userId` int(0) NULL DEFAULT NULL COMMENT '用户id',
            `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签说明',
            `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
            `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '升级时间',
            PRIMARY KEY (`tagId`) USING BTREE,
            UNIQUE INDEX `o`(`tagName`, `userId`) USING BTREE COMMENT '标签唯一'
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        // 创建漫画标签表
        DB::statement("CREATE TABLE IF NOT EXISTS `mangaTag`  (
            `mangaTagId` int(0) NOT NULL AUTO_INCREMENT COMMENT '漫画关联标签主键',
            `mangaId` int(0) NULL DEFAULT NULL COMMENT '漫画id',
            `tagId` int(0) NULL DEFAULT NULL COMMENT '标签id',
            `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
            `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
            PRIMARY KEY (`mangaTagId`) USING BTREE,
            UNIQUE INDEX `manga-tag`(`mangaId`, `tagId`) USING BTREE COMMENT '相同的标签不能多次添加'
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        // 新增3.3.7版本记录
        VersionSql::add([
            'version' => '3.3.7',
            'versionDescribe' => '新增自定义标签功能',
            'createTime' => '2023-07-29 16:03:00'
        ]);
    }
}
