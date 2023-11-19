<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 04:44:07
* @FilePath: /smanga-php/app/Http/Version/Version339.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version339
{
    public function __construct()
    {
        // 新增3.3.9版本记录
        VersionSql::add([
            'version' => '3.3.9',
            'versionDescribe' => '漫画管理新增搜索框',
            'createTime' => '2023-08-25 20:19:00'
        ]);
    }
}
