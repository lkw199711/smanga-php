<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:48:10
* @FilePath: /smanga-php/app/Http/Version/Version351.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version351
{
    public function __construct()
    {
        // 新增3.5.1版本记录
        VersionSql::add([
            'version' => '3.5.1',
            'versionDescribe' => '扫描时自动提取封面.',
            'createTime' => '2023-10-20 15:03:39'
        ]);
    }
}
