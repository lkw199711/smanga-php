<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 04:42:37
* @FilePath: /smanga-php/app/Http/Version/Version329.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version329
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.2.9',
            'versionDescribe' => '新增分页模式图片缓存,再次加载图片速度会加快.',
            'createTime' => '2023-5-12 22:51:22'
        ]);
    }
}
