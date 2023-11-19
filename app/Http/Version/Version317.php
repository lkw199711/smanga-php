<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:26:05
* @FilePath: /smanga-php/app/Http/Version/Version317.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version317
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.1.7',
            'versionDescribe' => '外置sql设置错误问题',
            'createTime' => '2023-3-18 00:27:31'
        ]);
    }
}
