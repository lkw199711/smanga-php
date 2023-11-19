<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:43:30
* @FilePath: /smanga-php/app/Http/Version/Version345.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version345
{
    public function __construct()
    {
        // 新增3.4.5版本记录
        VersionSql::add([
            'version' => '3.4.5',
            'versionDescribe' => '漫画详情页面新增收藏按钮',
            'createTime' => '2023-09-23 13:52:00'
        ]);
    }
}
