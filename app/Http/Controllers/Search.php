<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-08-25 15:07:22
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-23 00:28:27
 * @FilePath: /smanga-php/app/Http/Controllers/Search.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ListResponse;
use App\Models\ChapterSql;
use App\Models\MangaSql;
use App\Models\UserSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Search extends Controller
{
    /**
     * @description: 获取漫画列表
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $searchText = $request->post('searchText');
        $searchType = $request->post('searchType');
        $searchType = $request->post('searchType');
        $order = $request->post('order');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');

        $mediaLimit = UserSql::get_media_limit($userId);

        if ($searchType === 'manga') {
            $sqlList = MangaSql::manga_search($searchText, $mediaLimit, $order, $page, $pageSize, $userId);
            $res = new ListResponse($sqlList->list, $sqlList->count, '获取漫画搜索结果成功');
        }

        if ($searchType === 'chapter') {
            $sqlList = ChapterSql::chapter_search($searchText, $mediaLimit, $order, $page, $pageSize, $userId);
            $res = new ListResponse($sqlList->list, $sqlList->count, '获取章节搜索结果成功');
        }

        return new JsonResponse($res);
    }
}
