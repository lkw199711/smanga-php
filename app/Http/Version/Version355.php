<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:51:39
* @FilePath: /smanga-php/app/Http/Version/Version355.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version355
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.5.5',
            'versionDescribe' => '散图获取封面bug修复.',
            'createTime' => '2023-10-22 19:34:56'
        ]);
    }
}
