<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:47:30
* @FilePath: /smanga-php/app/Http/Version/Version350.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version350
{
    public function __construct()
    {
        // 新增3.5.0版本记录
        VersionSql::add([
            'version' => '3.5.0',
            'versionDescribe' => '菜单分类并修改图表.',
            'createTime' => '2023-10-18 23:47:35'
        ]);
    }
}
