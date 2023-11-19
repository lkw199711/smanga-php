<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:37:24
* @FilePath: /smanga-php/app/Http/Version/Version321.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version324
{
    public function __construct()
    {
        DB::statement("ALTER TABLE `manga` 
            MODIFY COLUMN `mangaName` varchar(191) CHARACTER SET utf8mb4 NOT NULL COMMENT '漫画名称' AFTER `pathId`,
            MODIFY COLUMN `mangaPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '漫画路径' AFTER `mangaName`,
            MODIFY COLUMN `mangaCover` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '漫画封面' AFTER `mangaPath`;
        ");

        DB::statement("ALTER TABLE `chapter` 
            MODIFY COLUMN `chapterName` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '章节名称' AFTER `pathId`,
            MODIFY COLUMN `chapterPath` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '章节路径' AFTER `chapterName`,
            MODIFY COLUMN `chapterType` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '文件类型' AFTER `chapterPath`,
            MODIFY COLUMN `chapterCover` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '章节封面' AFTER `browseType`;
        ");

        DB::statement("ALTER TABLE `media` 
            MODIFY COLUMN `mediaName` varchar(191) CHARACTER SET utf8mb4 NOT NULL COMMENT '媒体库名称' AFTER `mediaId`,
            MODIFY COLUMN `mediaCover` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '媒体库封面' AFTER `mediaType`;
        ");

        DB::statement("ALTER TABLE `user` 
            MODIFY COLUMN `userName` varchar(191) CHARACTER SET utf8mb4 NOT NULL COMMENT '用户名' AFTER `userId`,
            MODIFY COLUMN `nickName` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '昵称' AFTER `passWord`,
            MODIFY COLUMN `mediaLimit` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL AFTER `updateTime`;
        ");

        VersionSql::add([
            'version' => '3.2.4',
            'versionDescribe' => '适配表情文字',
            'createTime' => '2023-5-1 02:13:55'
        ]);
    }
}
