<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:26:44
* @FilePath: /smanga-php/app/Http/Version/Version318.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version318
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.1.8',
            'versionDescribe' => '新增视图切换功能, 解决文字展示不全的问题',
            'createTime' => '2023-4-1 13:23:08'
        ]);
    }
}
