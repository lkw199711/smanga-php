<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:30:43
* @FilePath: /smanga-php/app/Http/Version/Version320.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version320
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.2.0',
            'versionDescribe' => '新增搜索功能;处理扫描错误.',
            'createTime' => '2023-4-5 21:02:03'
        ]);
    }
}
