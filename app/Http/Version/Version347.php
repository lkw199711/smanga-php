<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:44:44
* @FilePath: /smanga-php/app/Http/Version/Version347.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version347
{
    public function __construct()
    {
        // 新增3.4.7版本记录
        VersionSql::add([
            'version' => '3.4.7',
            'versionDescribe' => '切换章节错误bug修复',
            'createTime' => '2023-09-24 23:30:00'
        ]);
    }
}
