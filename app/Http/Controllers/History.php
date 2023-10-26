<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-14 13:32:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 20:21:04
 * @FilePath: /php/laravel/app/Http/Controllers/History.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Models\HistorySql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class History extends Controller
{
    /**
     * @description: 获取历史记录
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        $userId = $request->post('userId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');
        $sqlList = HistorySql::get($userId, $page, $pageSize);

        $res = new ListResponse($sqlList->list, $sqlList->count, '历史记录列表获取成功');
        return new JsonResponse($res);
    }
    /**
     * @description: 获取漫画最后一次阅读记录
     * @param {Request} $request
     * @return {*}
     */
    public function get_latest(Request $request)
    {
        $userId = $request->post('userId');
        $mangaId = $request->post('mangaId');
        $latestChapter = HistorySql::get_latest($mangaId, $userId);
        $res = new InterfacesResponse($latestChapter, '最后阅读记录获取成功.', 'get latest-chapter success.');
        return new JsonResponse($res);
    }
    /**
     * @description: 新增历史记录
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        $userId = $request->post('userId');
        $mediaId = $request->post('mediaId');
        $mangaId = $request->post('mangaId');
        $mangaName = $request->post('mangaName');
        $chapterId = $request->post('chapterId');
        $chapterName = $request->post('chapterName');
        $chapterPath = $request->post('chapterPath');

        $data = ['userId' => $userId, 'mediaId' => $mediaId, 'mangaId' => $mangaId, 'chapterId' => $chapterId];
        $historyInfo = HistorySql::add($data);

        $res = new InterfacesResponse($historyInfo, '', 'history add success.');
        return new JsonResponse($res);
    }
    /**
     * @description: 删除历史记录
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $historyId = $request->post('historyId');
        $chapterId = HistorySql::info($historyId)->chapterId;
        
        $sqlRes = HistorySql::delete_by_chapter($chapterId);
        $res = new InterfacesResponse($sqlRes, '历史记录删除成功.', 'history delete success.');
        return new JsonResponse($res);
    }
}
