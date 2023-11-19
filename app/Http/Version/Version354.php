<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 02:50:29
* @FilePath: /smanga-php/app/Http/Version/Version354.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version354
{
    public function __construct()
    {
        VersionSql::add([
            'version' => '3.5.4',
            'versionDescribe' => '新增漫画排序逻辑.',
            'createTime' => '2023-10-22 16:00:38'
        ]);
    }
}
