<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:43:56
* @FilePath: /smanga-php/app/Http/Version/Version346.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version346
{
    public function __construct()
    {
        // 新增3.4.6版本记录
        VersionSql::add([
            'version' => '3.4.6',
            'versionDescribe' => 'pageSize逻辑调整',
            'createTime' => '2023-09-24 12:16:00'
        ]);
    }
}
