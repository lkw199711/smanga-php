<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:19:54
* @FilePath: /smanga-php/app/Http/Version/Version314.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version314
{
    public function __construct()
    {
        DB::statement("ALTER TABLE compress MODIFY COLUMN compressType enum('zip','rar','pdf','image','7z')");
        
        VersionSql::add([
            'version' => '3.1.4',
            'versionDescribe' => '添加7z,修复shell参数',
            'createTime' => '2023-2-28 8:32:00'
        ]);
    }
}
