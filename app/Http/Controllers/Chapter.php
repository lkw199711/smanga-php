<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-23 00:25:29
 * @FilePath: /php/laravel/app/Http/Controllers/Chapter.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Jobs\Compress;
use App\Models\ChapterSql;
use App\Models\CompressSql;
use App\Models\UserSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Chapter extends Controller
{
    /**
     * @description: 获取漫画章节
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $mangaId = $request->post('mangaId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');
        $order = $request->post('order');
        $keyWord = $request->post('keyWord');

        // 默认排序规则
        if (!$order) $order = 'id';

        // 获取媒体库权限
        $mediaLimit = UserSql::get_media_limit($userId);

        if ($keyWord) {
            return ChapterSql::chapter_search($keyWord, $mediaLimit, $order, $page, $pageSize, $userId);
        }

        if ($mangaId && $page) {
            // 正常获取漫画章节列表
            $sqlList = ChapterSql::get($mangaId, $page, $pageSize, $order);
        } elseif ($page) {
            // 在章节管理中获取章节列表
            $sqlList = ChapterSql::get_nomanga($mediaLimit, $page, $pageSize, $order);
        } elseif ($mangaId) {
            // 获取左侧章节菜单
            $sqlList = ChapterSql::get_nopage($mangaId, $order);
        }

        $res = new ListResponse($sqlList->list, $sqlList->count, '章节列表获取成功');
        return new JsonResponse($res);
    }

    /**
     * @description: 获取第一个章节
     * @param {Request} $request
     * @return {*}
     */
    public function get_first(Request $request)
    {
        $mangaId = $request->post('mangaId');
        $mangaInfo = ChapterSql::get_first($mangaId);

        if ($mangaInfo) {
            $res = new InterfacesResponse($mangaInfo, '', 'get mangaInfo success.');
        } else {
            $res = new ErrorResponse('获取漫画章节错误');
        }

        return new JsonResponse($res);
    }
    /**
     * @description: 修改漫画信息
     * @param {Request} $request
     * @return {*}
     */
    public function update(Request $request)
    {
        $chapterId = $request->post('chapterId');
        $chapterName = $request->post('chapterName');
        $chapterPath = $request->post('chapterPath');
        $chapterCover = $request->post('chapterCover');

        $data = ['chapterName' => $chapterName, 'chapterPath' => $chapterPath, 'chapterCover' => $chapterCover];

        $res = ChapterSql::chapter_update($chapterId, $data);

        if ($res) {
            $res = new InterfacesResponse($res, '章节修改成功');
        } else {
            $res = new ErrorResponse('章节修改错误');
        }

        return new JsonResponse($res);
    }
    /**
     * @description: 移除漫画信息
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $chapterId = $request->post('chapterId');

        $res = ChapterSql::chapter_delete($chapterId);

        if ($res) {
            $res = new InterfacesResponse($res, '章节删除成功');
        } else {
            $res = new ErrorResponse('章节删除错误');
        }

        return new JsonResponse($res);
    }
    public function image_list(Request $request)
    {
        $userId = $request->post('userId');
        $chapterId = $request->post('chapterId');

        $chapterIndo = ChapterSql::chapter_info($chapterId);
        $chapterPath = $chapterIndo->chapterPath;
        $chapterType = $chapterIndo->chapterType;

        if (Utils::is_compressed($chapterType)) {

            $compressInfo = CompressSql::compress_get_by_chapter($chapterId);

            if ($compressInfo) {
                if (array_search($compressInfo->compressStatus, ['compressing', 'compressed']) !== false) {
                    $list = Utils::get_file_list($compressInfo->compressPath);
                    $res = new ListResponse($list, count($list), $compressInfo->compressStatus);
                } else {
                    $res = new ListResponse([], 0, $compressInfo->compressStatus);
                }
            } else {
                // 调试模式下同步运行
                $dispatchSync = Utils::config_read('debug', 'dispatchSync');
                if ($dispatchSync) {
                    Compress::dispatchSync($userId, $chapterId);
                } else {
                    Compress::dispatch($userId, $chapterId)->onQueue('compress');
                }
                $res = new InterfacesResponse('', '正在进行压缩转换', 'uncompressed');
            }
        } else {
            $list = Utils::get_file_list($chapterPath);
            $res = new ListResponse($list, count($list), 'success.');
        }

        return new JsonResponse($res);
    }
}
