<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:32:49
* @FilePath: /smanga-php/app/Http/Version/Version321.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version321
{
    public function __construct()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `config` (
            `configId` int(0) NOT NULL AUTO_INCREMENT COMMENT '设置项主键',
            `userId` int(0) NULL COMMENT '关联的用户id',
            `userName` varchar(191) NULL COMMENT '关联的用户名',
            `configValue` text NULL COMMENT '设置的详细内容 json打包',
            `createTime` datetime(0) NULL COMMENT '设置的创建时间',
            `updateTime` datetime(0) NULL COMMENT '设置的最近升级时间',
            PRIMARY KEY (`configId`),
            UNIQUE INDEX `id`(`userId`) USING BTREE COMMENT '用户id唯一'
        )");

        VersionSql::add([
            'version' => '3.2.1',
            'versionDescribe' => '新增用户设置模块',
            'createTime' => '2023-4-22 18:12:57'
        ]);
    }
}
