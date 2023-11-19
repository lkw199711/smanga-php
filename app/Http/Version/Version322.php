<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:33:22
* @FilePath: /smanga-php/app/Http/Version/Version322.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version322
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.2.2',
            'versionDescribe' => '修复缓存与排序的bug',
            'createTime' => '2023-4-22 23:49:03'
        ]);
    }
}
