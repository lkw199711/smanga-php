<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-10-28 17:44:05
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-28 18:01:54
 * @FilePath: /smanga-php/app/Http/Controllers/Charts.php
 */


namespace App\Http\Controllers;

use App\Http\PublicClass\InterfacesResponse;
use App\Models\HistorySql;
use App\Models\MangaSql;
use App\Models\MangaTagSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Charts extends Controller
{
    /**
     * @description: 获取漫画列表
     * @param {Request} $request
     * @return {*}
     */
    public function browse(Request $request)
    {
        $res = new InterfacesResponse(MangaSql::type_group(), '', '');
        return new JsonResponse($res);
    }

    public function tag_count(Request $request)
    {
        $slice = $request->post('slice');
        $list = MangaTagSql::count_order($slice);
        $res = new InterfacesResponse($list, '', '');
        return new JsonResponse($res);
    }

    public function frequency(Request $request)
    {
        $userId = $request->post('userId');
        $slice = $request->post('slice');
        $list = HistorySql::frequency($userId, $slice);
        $res = new InterfacesResponse($list, '', '');
        return new JsonResponse($res);
    }

    public function ranking(Request $request)
    {
        $userId = $request->post('userId');
        $slice = $request->post('slice');
        $list = HistorySql::ranking($slice);
        $res = new InterfacesResponse($list, '', '');
        return new JsonResponse($res);
    }
}
