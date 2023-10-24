<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 19:03:12
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-25 00:35:03
 * @FilePath: \lar-demo\app\Http\Controllers\BookMark.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Models\BookMarkSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookMark extends Controller
{
    public function get(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');

        if ($page) {
            // 模型处理数据
            $sqlList = BookMarkSql::get($userId, $page, $pageSize);
        } else {
            $sqlList = BookMarkSql::get_nopage($userId);
        }

        $res = new ListResponse($sqlList->list, $sqlList->count);
        return new JsonResponse($res);
    }
    
    public function all(Request $request)
    {
        // 接受参数
        ['page' => $page, 'pageSize' => $pageSize] = $request->input();

        // 模型处理数据
        $sqlList = BookMarkSql::get($page, $pageSize);

        $res = new ListResponse($sqlList->list, $sqlList->count);
        return new JsonResponse($res);
    }

    /**
     * @description: 新增书签
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        // 书签存放路径
        $bookmarkPosterPath = Utils::get_env('SMANGA_BOOKMARK');

        // 接受参数
        ['userId' => $userId, 'mediaId' => $mediaId, 'mangaId' => $mangaId, 'mangaName' => $mangaName, 'chapterId' => $chapterId, 'chapterPath' => $chapterPath, 'chapterName' => $chapterName, 'page' => $page, 'pageImage' => $pageImage, 'browseType' => $browseType
        ] = $request->post();

        // 生成书签封面存放在、poster
        $md5 = md5($pageImage);
        $target = "$bookmarkPosterPath/$md5.png";
        copy($pageImage, $target);

        // 生成insert的数据
        $data = [
            'userId' => $userId, 'mediaId' => $mediaId, 'mangaId' => $mangaId, 'chapterId' => $chapterId, 'page' => $page, 'pageImage' => $pageImage, 'browseType' => $browseType
        ];

        $bookmarkInfo = BookMarkSql::add($data);

        $res = new InterfacesResponse($bookmarkInfo, '书签新增成功', 'bookmark add success.');
        
        return new JsonResponse($res);
    }

    /**
     * @description: 删除书签
     * @param {Request} $request
     * @return {*}
     */
    public function remove(Request $request)
    {
        $bookmarkId = $request->post('bookmarkId');
        $sqlRes = BookMarkSql::remove($bookmarkId);

        $res = new InterfacesResponse($sqlRes, '书签删除成功', 'bookmark remove success.');

        return new JsonResponse($res);
    }
}
