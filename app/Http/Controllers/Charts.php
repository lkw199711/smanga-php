<?php


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

    public function tag_count()
    {
        $list = MangaTagSql::count_order();
        // $list1  = array_slice($list->items, 0, 5);
        $res = new InterfacesResponse($list, '', '');
        return new JsonResponse($res);
    }

    public function frequency(Request $request)
    {
        $userId = $request->post('userId');
        $list = HistorySql::frequency($userId);
        $res = new InterfacesResponse($list, '', '');
        return new JsonResponse($res);
    }

    public function ranking(Request $request)
    {
        $userId = $request->post('userId');
        $list = HistorySql::ranking($userId);
        $res = new InterfacesResponse($list, '', '');
        return new JsonResponse($res);
    }
    
}
