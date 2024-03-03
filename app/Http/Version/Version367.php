<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-09 20:36:59
* @FilePath: /smanga-php/app/Http/Version/Version361.php
*/

namespace App\Http\Version;

use App\Http\Controllers\Utils;
use App\Models\PathSql;
use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version367
{
    public function __construct()
    {
        
        // 封面压缩大小
        DB::statement("ALTER TABLE `path` 
            ADD COLUMN `pathType` varchar(255) NULL COMMENT '路径类型(主要目录|附属目录)' AFTER `mediaId`;
        ");

        DB::statement("ALTER TABLE `manga` 
            ADD COLUMN `parentPath` varchar(255) NULL COMMENT '父级目录' AFTER `publishDate`;
        ");

        DB::statement("ALTER TABLE `manga` 
            ADD COLUMN `subTitle` varchar(255) NULL COMMENT '副标题 用于搜索' AFTER `publishDate`;
        ");

        DB::statement("ALTER TABLE `chapter` 
            ADD COLUMN `subTitle` varchar(255) NULL COMMENT '副标题 用于搜索' AFTER `chapterCover`;
        ");
        

        // pathType默认值
        PathSql::where('pathType', '<>', 'sub')->update(['pathType' => 'main']);

        VersionSql::add([
            'version' => '3.6.7',
            'versionDescribe' => '新增副目录规则.繁简体搜索兼容.',
            'createTime' => '2024-03-04 05:15:30'
        ]);
    }
}
