<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-23 18:14:40
 * @FilePath: /php/laravel/app/Http/Controllers/Manga.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Jobs\DeleteManga;
use App\Models\MangaSql;
use App\Models\UserSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Manga extends Controller
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
        $mediaId = $request->post('mediaId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');
        $keyWord = $request->post('keyWord');
        $order = $request->post('order');

        $mediaLimit = UserSql::get_media_limit($userId);

        if ($keyWord) {
            $sqlList = MangaSql::manga_search($keyWord, $mediaLimit, $order, $page, $pageSize, $userId);
        }

        if ($mediaId) {
            // 通过媒体库获取漫画
            $sqlList = MangaSql::get($page, $pageSize, $mediaId, $mediaLimit, $userId, $order);
        } else {
            // 获取全部漫画
            $sqlList = MangaSql::get_nomedia($page, $pageSize, $mediaLimit);
        }
        
        $res = new ListResponse($sqlList->list, $sqlList->count, '获取漫画列表成功.');
        return new JsonResponse($res);
    }
    /**
     * @description: 修改漫画信息
     * @param {Request} $request
     * @return {*}
     */
    public function update(Request $request)
    {
        $mangaId = $request->post('mangaId');
        $mangaName = $request->post('mangaName');
        $mangaPath = $request->post('mangaPath');
        $mangaCover = $request->post('mangaCover');
        $browseType = $request->post('browseType');
        $removeFirst = $request->post('removeFirst');
        $direction = $request->post('direction');

        $data = ['mangaName' => $mangaName, 'mangaPath' => $mangaPath, 'mangaCover' => $mangaCover, 'browseType' => $browseType, 'removeFirst' => $removeFirst, 'direction' => $direction];

        // 执行sql操作
        $sqlRes = MangaSql::manga_update($mangaId, $data);

        if ($sqlRes) {
            $res = new InterfacesResponse($sqlRes, '漫画修改成功', 'Manga update success.');
        } else {
            $res = new ErrorResponse('漫画修改失败');
        }

        return new JsonResponse($res);
    }
    /**
     * @description: 移除漫画
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $mangaId = $request->post('mangaId');

        $sqlRes = MangaSql::manga_delete($mangaId);

        JobDispatch::handle('DeleteManga', 'scan', $mangaId);

        if ($sqlRes) {
            $res = new InterfacesResponse($sqlRes, '漫画删除成功', 'Manga delete success.');
        } else {
            $res = new ErrorResponse('漫画删除失败');
        }

        return new JsonResponse($res);
    }

    /**
     * @description: 根据标签获取漫画
     * @param {Request} $request
     * @return {*}
     */
    public function get_by_tags(Request $request)
    {
        $tagIds = $request->post('tagIds');
        $userId = $request->post('userId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');
        $order = $request->post('order');

        $tagidArr = explode(',', $tagIds);

        $sqlList = MangaSql::get_by_tags($tagidArr, $page, $pageSize, $order);

        $res = new ListResponse($sqlList->list, $sqlList->count);

        return new JsonResponse($res);
    }

    /**
     * @description: 获取漫画元数据
     * @param {Request} $request
     * @return {*}
     */
    public function get_manga_info(Request $request)
    {
        $mangaId = $request->post('mangaId');
        $userId = $request->post('userId');

        $res = new InterfacesResponse(MangaSql::get_manga_info($mangaId, $userId), '', '获取漫画信息成功');

        return new JsonResponse($res);
    }
}
