<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-03 16:34:41
* @FilePath: /smanga-php/app/Http/Version/Version361.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version361
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.6.2',
            'versionDescribe' => '支持配置ssl证书.',
            'createTime' => '2023-12-03 16:35:00'
        ]);
    }
}
