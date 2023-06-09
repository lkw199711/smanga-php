<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-06-13 21:06:58
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-06-13 21:32:45
 * @FilePath: /smanga-php/app/Http/Controllers/Scan.php
 */

namespace App\Http\Controllers;

use App\Models\LogSql;
use Illuminate\Http\Request;

class Log extends Controller
{
    /**
     * @description: 获取日志
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');

        return LogSql::get($page, $pageSize);
    }
}
