<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-06-13 21:06:58
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-25 01:15:38
 * @FilePath: /smanga-php/app/Http/Controllers/Scan.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ListResponse;
use App\Models\LogSql;
use Illuminate\Http\JsonResponse;
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

        $sqlList = LogSql::get($page, $pageSize);

        $res = new ListResponse($sqlList->list,$sqlList->count,'获取日志列表成功.');
        return new JsonResponse($res);
    }
}
