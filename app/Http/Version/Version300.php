<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 03:12:30
* @FilePath: /smanga-php/app/Http/Version/Version300.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version300
{
    public function __construct()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `bookmark`  (
            `bookmarkId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `mediaId` int(11) NULL DEFAULT NULL,
            `mangaId` int(11) NULL DEFAULT NULL,
            `chapterId` int(11) NULL DEFAULT NULL,
            `userId` int(11) NULL DEFAULT NULL,
            `browseType` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'single',
            `page` int(11) NULL DEFAULT NULL,
            `pageImage` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
            `createTime` datetime(0) NULL DEFAULT NULL,
            PRIMARY KEY (`bookmarkId`) USING BTREE,
            UNIQUE INDEX `opage`(`chapterId`, `page`) USING BTREE
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `chapter`  (
                `chapterId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '章节记录',
                `mangaId` int(11) NULL DEFAULT NULL COMMENT '漫画id',
                `mediaId` int(11) NULL DEFAULT NULL COMMENT '媒体库id',
                `pathId` int(11) NULL DEFAULT NULL COMMENT '路径id',
                `chapterName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '章节名称',
                `chapterPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '章节路径',
                `chapterType` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '文件类型',
                `browseType` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式',
                `chapterCover` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '章节封面',
                `picNum` int(11) NULL DEFAULT NULL COMMENT '图片数量',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '最新修改时间',
                PRIMARY KEY (`chapterId`) USING BTREE,
                UNIQUE INDEX `oname`(`mangaId`, `chapterName`) USING BTREE
            ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `compress`  (
                `compressId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '转换id',
                `compressType` enum('zip','rar','pdf','image') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '转换类型',
                `compressPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '转换路径',
                `compressStatus` enum('uncompressed','compressing','compressed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '转换状态',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '转换时间',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
                `imageCount` int(10) NULL DEFAULT NULL COMMENT '图片总数',
                `mediaId` int(11) NULL DEFAULT NULL COMMENT '媒体库id',
                `mangaId` int(11) NULL DEFAULT NULL COMMENT '漫画id',
                `chapterId` int(11) NULL DEFAULT NULL COMMENT '章节id',
                `chapterPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '章节路径',
                `userId` int(11) NULL DEFAULT NULL COMMENT '用户标识',
                PRIMARY KEY (`compressId`) USING BTREE,
                UNIQUE INDEX `id`(`compressId`) USING BTREE,
                UNIQUE INDEX `oChapter`(`chapterId`) USING BTREE
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `history`  (
                `historyId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '历史记录id',
                `userId` int(11) NULL DEFAULT NULL COMMENT '用户id',
                `mediaId` int(11) NULL DEFAULT NULL COMMENT '媒体库id',
                `mangaId` int(11) NULL DEFAULT NULL COMMENT '漫画id',
                `mangaName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '漫画名称',
                `chapterId` int(11) NULL DEFAULT NULL COMMENT '章节id',
                `chapterName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '章节名称',
                `chapterPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '章节路径',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                PRIMARY KEY (`historyId`) USING BTREE
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `login`  (
                `loginId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '登录记录',
                `userId` int(11) NULL DEFAULT NULL COMMENT '用户记录',
                `userName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名',
                `nickName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '昵称',
                `request` int(1) NULL DEFAULT NULL COMMENT '0->成功 1->失败',
                `ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ip地址',
                `loginTime` datetime(0) NULL DEFAULT NULL COMMENT '登录时间',
                PRIMARY KEY (`loginId`) USING BTREE
            ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `manga`  (
                `mangaId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '漫画id',
                `mediaId` int(11) NOT NULL COMMENT '媒体库id',
                `pathId` int(11) NULL DEFAULT NULL COMMENT '路径id',
                `mangaName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '漫画名称',
                `mangaPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '漫画路径',
                `mangaCover` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '漫画封面',
                `chapterCount` int(191) NULL DEFAULT NULL COMMENT '章节总数',
                `browseType` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
                `direction` int(1) NULL DEFAULT 1 COMMENT '翻页方向 0 左到右; 1右到左',
                `removeFirst` int(1) NULL DEFAULT 0 COMMENT '剔除首页 01',
                PRIMARY KEY (`mangaId`) USING BTREE,
                UNIQUE INDEX `oname`(`mediaId`, `mangaPath`) USING BTREE
            ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `media`  (
                `mediaId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '媒体库id',
                `mediaName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '媒体库名称',
                `mediaType` int(1) NOT NULL COMMENT '媒体库类型 0->漫画 1->单本',
                `mediaCover` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '媒体库封面',
                `directoryFormat` int(1) NULL DEFAULT NULL COMMENT '目录格式 \r\n0 漫画 -> 章节 -> 图片\r\n1 目录 -> 漫画 -> 章节 -> 图片\r\n2 漫画 -> 图片\r\n3 目录 -> 漫画 -> 图片\r\n\r\n23为单本',
                `fileType` int(1) NULL DEFAULT NULL COMMENT '文件类型 0->图片 1->pdf',
                `defaultBrowse` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'flow' COMMENT '默认浏览类型',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '最新修改时间',
                `direction` int(1) NULL DEFAULT 1 COMMENT '翻页方向 0 左到右; 1右到左',
                `removeFirst` int(1) NULL DEFAULT 0 COMMENT '剔除首页 01',
                PRIMARY KEY (`mediaId`) USING BTREE,
                UNIQUE INDEX `nameId`(`mediaId`, `mediaName`) USING BTREE,
                UNIQUE INDEX `name`(`mediaName`) USING BTREE
            ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `path`  (
                `pathId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '路径id',
                `mediaId` int(11) NOT NULL COMMENT '媒体库id',
                `path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路径',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                PRIMARY KEY (`pathId`) USING BTREE,
                UNIQUE INDEX `opath`(`mediaId`, `path`) USING BTREE
            ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `user`  (
                `userId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
                `userName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
                `passWord` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
                `nickName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '昵称',
                `registerTime` datetime(0) NULL DEFAULT NULL COMMENT '注册时间',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
                PRIMARY KEY (`userId`, `userName`) USING BTREE,
                UNIQUE INDEX `username`(`userName`) USING BTREE
            ) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `version` (
            `versionId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '版本记录',
            `versionDescribe` VARCHAR(191) NULL DEFAULT NULL COMMENT '版本描述',
            `version` varchar(191) NULL DEFAULT NULL COMMENT 'version number',
            `createTime` datetime(0) NULL DEFAULT NULL COMMENT 'createTime',
            `updateTime` datetime(0) NULL DEFAULT NULL COMMENT 'updateTime',
            PRIMARY KEY (`versionId`) USING BTREE,
            UNIQUE INDEX `version`(`version`) USING BTREE);
        ");

        // 插入smanga的用户名密码
        DB::statement("INSERT INTO `user` VALUES (1, 'smanga', 'f7f1fe7186209906a97756ff912bb644', NULL, NULL, NULL);");
        
        VersionSql::add([
            'version' => '3.0.0',
            'versionDescribe' => '项目初始化',
            'createTime' => '2023-1-30 00:00:00'
        ]);
    }
}
