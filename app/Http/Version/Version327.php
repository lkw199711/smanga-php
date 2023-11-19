<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:43:55
* @FilePath: /smanga-php/app/Http/Version/Version321.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version327
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.2.7',
            'versionDescribe' => '修正裁切尺寸',
            'createTime' => '2023-5-7 13:25:12'
        ]);
    }
}
