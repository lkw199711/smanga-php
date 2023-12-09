<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-06 23:59:16
* @FilePath: /smanga-php/app/Http/Version/Version334.php
*/

namespace App\Http\Version;

use App\Http\Controllers\Utils;
use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version334
{
    public function __construct()
    {
        // 初始化自动扫描间隔
        Utils::attribute_init('scan', 'interval', 1 * 24 * 60 * 60);
        
        // 新增3.3.4版本记录
        VersionSql::add([
            'version' => '3.3.4',
            'versionDescribe' => '新增自动扫描时间设置',
            'createTime' => '2023-07-16 17:34:00'
        ]);
    }
}
