<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:49:00
* @FilePath: /smanga-php/app/Http/Version/Version353.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version353
{
    public function __construct()
    {
        // 新增3.5.3版本记录
        VersionSql::add([
            'version' => '3.5.3',
            'versionDescribe' => '散图漫画封面删除bug修复.',
            'createTime' => '2023-10-21 21:19:15'
        ]);
    }
}
