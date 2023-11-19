<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:37:09
* @FilePath: /smanga-php/app/Http/Version/Version338.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version338
{
    public function __construct()
    {
        // 创建角色表
        DB::statement("CREATE TABLE IF NOT EXISTS `character`  (
                `characterId` int(0) NOT NULL AUTO_INCREMENT,
                `characterName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `characterPicture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `createTime` datetime(0) NULL DEFAULT NULL,
                `updateTime` datetime(0) NULL DEFAULT NULL,
                `mangaId` int(0) NULL DEFAULT NULL,
                PRIMARY KEY (`characterId`) USING BTREE,
                UNIQUE INDEX `o`(`characterName`, `mangaId`) USING BTREE COMMENT '同一漫画不能有重名的角色'
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        // 创建元数据表
        DB::statement("CREATE TABLE IF NOT EXISTS `meta`  (
            `metaId` int(0) NOT NULL AUTO_INCREMENT,
            `metaType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
            `mangaId` int(0) NULL DEFAULT NULL,
            `metaFile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
            `metaContent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
            `createTime` datetime(0) NULL DEFAULT NULL,
            `updateTime` datetime(0) NULL DEFAULT NULL,
            PRIMARY KEY (`metaId`) USING BTREE,
            UNIQUE INDEX `o`(`mangaId`, `metaFile`) USING BTREE COMMENT '同个漫画的某个元数据不能引入两次'
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        // 在manga表中新增元数据字段
        DB::statement("ALTER TABLE `manga` 
            ADD COLUMN `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '元数据标题',
            ADD COLUMN `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '作者',
            ADD COLUMN `star` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '评价',
            ADD COLUMN `describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '作品简介',
            ADD COLUMN `publishDate` date NULL DEFAULT NULL COMMENT '发布日期';
        ");

        // 新增3.3.8版本记录
        VersionSql::add([
            'version' => '3.3.8',
            'versionDescribe' => '新增元数据刮削功能',
            'createTime' => '2023-08-18 03:15:00'
        ]);
    }
}
