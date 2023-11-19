<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:40:42
* @FilePath: /smanga-php/app/Http/Version/Version341.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version341
{
    public function __construct()
    {
        // 新增3.4.1版本记录
        VersionSql::add([
            'version' => '3.4.1',
            'versionDescribe' => '临时增加登出按钮',
            'createTime' => '2023-09-13 02:07:00'
        ]);
    }
}
