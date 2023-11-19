<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:27:42
* @FilePath: /smanga-php/app/Http/Version/Version319.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version319
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.1.9',
            'versionDescribe' => '新增排序方式切换功能',
            'createTime' => '2023-4-1 23:33:43'
        ]);
    }
}
