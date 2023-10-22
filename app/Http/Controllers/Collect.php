<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 19:03:12
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-22 15:41:36
 * @FilePath: \lar-demo\app\Http\Controllers\Collect.php
 */

namespace App\Http\Controllers;

use App\Models\CollectSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;

class Collect extends Controller
{
    /**
     * @description: 获取收藏列表
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');
        $collectType = $request->post('collectType');
        $order = $request->post('order');

        // 模型处理数据
        if ($collectType === 'manga') {
            $sqlList =  CollectSql::get_manga($userId, $page, $pageSize, $order);
        } else {
            $sqlList =  CollectSql::get_chapter($userId, $page, $pageSize, $order);
        }

        $res = new ListResponse($sqlList->list, $sqlList->count, '收藏列表获取成功');
        return new JsonResponse($res);
    }

    /**
     * @description: 获取所有的收藏记录
     * @param {Request} $request
     * @return {*}
     */
    public function all(Request $request)
    {
        // 接受参数
        ['page' => $page, 'pageSize' => $pageSize] = $request->input();

        // 模型处理数据
        $sqlList = CollectSql::get($page, $pageSize);

        $res = new ListResponse($sqlList->list, $sqlList->count, '收藏列表获取成功');
        return new JsonResponse($res);
    }

    /**
     * @description: 新增收藏
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        // 接受参数
        ['userId' => $userId, 'mediaId' => $mediaId, 'mangaId' => $mangaId, 'collectType' => $collectType] = $request->post();
        $chapterId = $request->post('chapterId');
        $chapterName = $request->post('chapterName');
        $mangaName = $request->post('mangaName');

        // 生成insert的数据
        $data = ['userId' => $userId, 'mediaId' => $mediaId, 'mangaId' => $mangaId, 'collectType' => $collectType];

        if ($collectType === 'chapter') {
            $data['chapterId'] = $chapterId;
            $data['chapterName'] = $chapterName;
        }

        if ($collectType === 'manga') {
            $data['mangaName'] = $mangaName;
        }

        $request = CollectSql::add($data);

        $res = new InterfacesResponse($request, '添加收藏成功');

        return new JsonResponse($res);
    }

    /**
     * @description: 删除收藏
     * @param {Request} $request
     * @return {*}
     */
    public function remove(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $collectType = $request->post('collectType');
        $targetId = $request->post('targetId');
        $collectId = $request->post('collectId');

        // 有id 则根据id删除
        if ($collectId) $sqlRes = CollectSql::remove($collectId);

        if ($collectType === 'manga') $sqlRes = CollectSql::remove_manga($userId, $targetId);

        if ($collectType === 'chapter') $sqlRes = CollectSql::remove_chapter($userId, $targetId);

        $res = new InterfacesResponse($sqlRes, '移除收藏成功');

        return new JsonResponse($res);
    }

    /**
     * @description: 查询漫画是否收藏
     * @param {Request} $request
     * @return {*}
     */
    public function is_collect(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $collectType = $request->post('collectType');
        $targetId = $request->post('targetId');

        if ($collectType === 'manga') $sqlRes = CollectSql::is_manga_collected($userId, $targetId);

        if ($collectType === 'chapter') $sqlRes = CollectSql::is_chapter_collected($userId, $targetId);

        $res = new InterfacesResponse(!!$sqlRes, '', $sqlRes ? '漫画已被收藏' : '漫画没有被收藏');

        return new jsonResponse($res);
    }
}
