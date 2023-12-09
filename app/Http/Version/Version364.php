<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-09 20:36:59
* @FilePath: /smanga-php/app/Http/Version/Version361.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version364
{
    public function __construct()
    {        
        VersionSql::add([
            'version' => '3.6.4',
            'versionDescribe' => '修复ssl证书路径回显以及封面压缩大小设置.',
            'createTime' => '2023-12-09 20:37:38'
        ]);
    }
}
