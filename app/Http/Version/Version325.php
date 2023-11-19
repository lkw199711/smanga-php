<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:40:38
* @FilePath: /smanga-php/app/Http/Version/Version325.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version325
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.2.5',
            'versionDescribe' => '修改初始化流程',
            'createTime' => '2023-5-1 11:45:45'
        ]);
    }
}
