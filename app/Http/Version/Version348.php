<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:46:20
* @FilePath: /smanga-php/app/Http/Version/Version348.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version348
{
    public function __construct()
    {
        // 生成任务队列表
        DB::statement("CREATE TABLE IF NOT EXISTS `lastRead` (
                `lastReadId` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `page` int(0) NOT NULL,
                `chapterId` int(0) NOT NULL,
                `mangaId` int(0) NOT NULL,
                `userId` int(0) NOT NULL,
                `createTime` datetime(0) NULL DEFAULT NULL,
                `updateTime` datetime(0) NULL DEFAULT NULL,
                PRIMARY KEY (`lastReadId`) USING BTREE
                ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

        // 新增3.4.8版本记录
        VersionSql::add([
            'version' => '3.4.8',
            'versionDescribe' => '新增"继续阅读模块",准确定位"继续阅读"功能.',
            'createTime' => '2023-10-08 19:33:16'
        ]);
    }
}
