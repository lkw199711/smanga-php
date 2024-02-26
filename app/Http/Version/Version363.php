<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-06 23:53:32
* @FilePath: /smanga-php/app/Http/Version/Version361.php
*/

namespace App\Http\Version;

use App\Http\Controllers\Utils;
use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version363
{
    public function __construct()
    {
        // 封面压缩大小
        Utils::attribute_init('poster', 'size', 100);
        
        VersionSql::add([
            'version' => '3.6.3',
            'versionDescribe' => '支持封面压缩,默认为至100k以下.',
            'createTime' => '2023-12-07 08:43:41'
        ]);
    }
}