<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-09-24 23:19:04
 * @FilePath: /php/laravel/app/Http/Controllers/Chapter.php
 */

namespace App\Http\Controllers;

use App\Jobs\Compress;
use App\Models\ChapterSql;
use App\Models\CompressSql;
use App\Models\UserSql;
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
            return ChapterSql::get($mangaId, $page, $pageSize, $order);
        } elseif ($page) {
            // 在章节管理中获取章节列表
            return ChapterSql::get_nomanga($mediaLimit, $page, $pageSize, $order);
        } elseif ($mangaId) {
            // 获取左侧章节菜单
            return ChapterSql::get_nopage($mangaId, $order);
        }
    }
    
    /**
     * @description: 获取第一个章节
     * @param {Request} $request
     * @return {*}
     */
    public function get_first(Request $request){
        $mangaId = $request->post('mangaId');

        return ChapterSql::get_first($mangaId);
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

        return ChapterSql::chapter_update($chapterId, $data);
    }
    /**
     * @description: 移除漫画信息
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $chapterId = $request->post('chapterId');

        return ChapterSql::chapter_delete($chapterId);
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
                    return ['code' => 0, 'list' => $list, 'status' => $compressInfo->compressStatus];
                } else {
                    return ['code' => 0, 'list' => [], 'status' => $compressInfo->compressStatus];
                }
            } else {
                // 调试模式下同步运行
                $dispatchSync = Utils::config_read('debug', 'dispatchSync');
                if ($dispatchSync) {
                    Compress::dispatchSync($userId, $chapterId);
                } else {
                    Compress::dispatch($userId, $chapterId)->onQueue('compress');
                }

                return ['code' => 0, 'message' => '正在进行压缩转换', 'status' => 'uncompressed'];
            }
        } else {
            $list = Utils::get_file_list($chapterPath);
            return ['code' => 0, 'list' => $list, 'status' => 'success'];
        }
    }
}
