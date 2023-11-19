<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:21:42
* @FilePath: /smanga-php/app/Http/Version/Version315.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version315
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.1.5',
            'versionDescribe' => '条漫模式新增书签支持',
            'createTime' => '2023-3-4 14:57:00'
        ]);
    }
}
