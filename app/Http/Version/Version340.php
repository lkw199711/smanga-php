<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:38:50
* @FilePath: /smanga-php/app/Http/Version/Version340.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version340
{
    public function __construct()
    {
        // 新增3.4.0版本记录
        VersionSql::add([
            'version' => '3.4.0',
            'versionDescribe' => '自动解压设置项',
            'createTime' => '2023-08-26 06:33:00'
        ]);
    }
}
