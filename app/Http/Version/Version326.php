<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:43:32
* @FilePath: /smanga-php/app/Http/Version/Version321.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version326
{
    public function __construct()
    {
        DB::statement("ALTER TABLE bookmark MODIFY COLUMN `browseType` enum('flow','single','double','half') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'single' AFTER `userId`;
            ALTER TABLE chapter MODIFY COLUMN `browseType` enum('flow','single','double','half') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式' AFTER `chapterType`;
            ALTER TABLE manga MODIFY COLUMN `browseType` enum('flow','single','double','half') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式' AFTER `chapterCount`;
            ALTER TABLE media MODIFY COLUMN `defaultBrowse` enum('flow','single','double','half') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'flow' COMMENT '默认浏览类型' AFTER `fileType`;
        ");

        VersionSql::add([
            'version' => '3.2.6',
            'versionDescribe' => '新增对半裁切模式',
            'createTime' => '2023-5-3 23:49:36'
        ]);
    }
}
