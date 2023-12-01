<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-12-01 23:12:47
* @FilePath: /smanga-php/app/Http/Version/Version316.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version316
{
    public function __construct()
    {
        DB::statement("ALTER TABLE user ADD `mediaLimit` varchar(191);");
        DB::statement("ALTER TABLE user ADD `editMedia` int(1) DEFAULT 1;");
        DB::statement("ALTER TABLE user ADD `editUser` int(1) DEFAULT 1;");

        VersionSql::add([
            'version' => '3.1.6',
            'versionDescribe' => '新增用户权限管理',
            'createTime' => '2023-3-5 18:05:00'
        ]);
    }
}
