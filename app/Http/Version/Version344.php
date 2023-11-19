<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:42:46
* @FilePath: /smanga-php/app/Http/Version/Version344.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version344
{
    public function __construct()
    {
        // 新增3.4.4版本记录
        VersionSql::add([
            'version' => '3.4.4',
            'versionDescribe' => '修复解压路径获取错误的问题',
            'createTime' => '2023-09-21 08:16:00'
        ]);
    }
}
