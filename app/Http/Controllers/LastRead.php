<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-14 13:32:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-12 23:34:11
 * @FilePath: /php/laravel/app/Http/Controllers/LastRead.php
 */

namespace App\Http\Controllers;

use App\Models\LastReadSql;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;

class LastRead extends Controller
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

        $sqlList = LastReadSql::get($userId, $page, $pageSize);

        $res = new ListResponse($sqlList->list, $sqlList->count, '阅读记录获取成功');
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

        $info = LastReadSql::get_latest($mangaId, $userId);

        $text =  $info ? '获取最终记录成功' : '无最终阅读记录';
        $res = new InterfacesResponse($info, '', $text);

        return new JsonResponse($res);
    }

    /**
     * @description: 新增历史记录
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        $page = $request->post('page');
        $finish = $request->post('finish');
        $chapterId = $request->post('chapterId');
        $mangaId = $request->post('mangaId');
        $userId = $request->post('userId');

        // 页码错误 退出
        if (!$page) return;
        if (!$mangaId) return;
        if (!$chapterId) return;

        // 传递过来的值为字符串的false 所以需要转化一下
        $finish = Utils::bool($finish) ? 1 : 0;

        $data = ['page' => $page, 'finish' => $finish, 'mangaId' => $mangaId, 'chapterId' => $chapterId, 'userId' => $userId];

        return LastReadSql::add($mangaId, $data);
    }

    /**
     * @description: 删除历史记录
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $historyId = $request->post('historyId');
        $chapterId = LastReadSql::info($historyId)->chapterId;
        return LastReadSql::delete_by_chapter($chapterId);
    }
}
