<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:34:06
* @FilePath: /smanga-php/app/Http/Version/Version321.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version323
{
    public function __construct()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `collect` (
            `collectId` int(0) NOT NULL AUTO_INCREMENT COMMENT '收藏id',
            `collectType` varchar(255) NULL COMMENT '收藏类型',
            `userId` int(0) NOT NULL COMMENT '用户id',
            `mediaId` int(0) NULL COMMENT '媒体库id',
            `mangaId` int(0) NULL COMMENT '漫画id',
            `mangaName` varchar(255) NULL COMMENT '漫画名称',
            `chapterId` int(0) NULL COMMENT '章节id',
            `chapterName` varchar(255) NULL COMMENT '章节名称',
            `createTime` datetime(0) NULL COMMENT '收藏日期',
            PRIMARY KEY (`collectId`),
            UNIQUE INDEX `uManga`(`collectType`, `mangaId`) USING BTREE COMMENT '漫画id不允许重复',
            UNIQUE INDEX `uChapter`(`collectType`, `chapterId`) USING BTREE COMMENT '章节id不允许重复')
        ");

        VersionSql::add([
            'version' => '3.2.3',
            'versionDescribe' => '新增收藏模块',
            'createTime' => '2023-4-24 23:36:57'
        ]);
    }
}
