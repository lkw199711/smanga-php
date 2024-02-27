<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-09 20:36:59
* @FilePath: /smanga-php/app/Http/Version/Version361.php
*/

namespace App\Http\Version;

use App\Http\Controllers\Utils;
use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version365
{
    public function __construct()
    {
        // 封面压缩大小
        Utils::attribute_init('compress', 'saveDuration', 100);

        VersionSql::add([
            'version' => '3.6.5',
            'versionDescribe' => '新增文件保存时长设置.',
            'createTime' => '2024-02-26 05:04:32'
        ]);
    }
}
