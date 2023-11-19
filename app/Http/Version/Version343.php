<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:42:10
* @FilePath: /smanga-php/app/Http/Version/Version343.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version343
{
    public function __construct()
    {
        // 新增3.4.3版本记录
        VersionSql::add([
            'version' => '3.4.3',
            'versionDescribe' => '章节管理新增搜索框',
            'createTime' => '2023-09-14 10:55:00'
        ]);
    }
}
