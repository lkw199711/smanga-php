<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:44:12
* @FilePath: /smanga-php/app/Http/Version/Version321.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version328
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.2.8',
            'versionDescribe' => '新增图片下载功能',
            'createTime' => '2023-5-8 00:09:00'
        ]);
    }
}
