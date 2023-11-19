<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:54:20
* @FilePath: /smanga-php/app/Http/Version/Version360.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version360
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.6.0',
            'versionDescribe' => '封面图片缓存,添加骨架屏动画.',
            'createTime' => '2023-11-01 01:08:17'
        ]);
    }
}
