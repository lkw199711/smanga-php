<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:40:51
* @FilePath: /smanga-php/app/Http/Version/Version342.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version342
{
    public function __construct()
    {
        // 新增3.4.2版本记录
        VersionSql::add([
            'version' => '3.4.2',
            'versionDescribe' => '移动端滑动翻页功能',
            'createTime' => '2023-09-13 20:30:00'
        ]);
    }
}
