<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:49:00
* @FilePath: /smanga-php/app/Http/Version/Version352.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version352
{
    public function __construct()
    {
        // 新增3.5.2版本记录
        VersionSql::add([
            'version' => '3.5.2',
            'versionDescribe' => '目录扫描方法替换.',
            'createTime' => '2023-10-21 18:45:52'
        ]);
    }
}
