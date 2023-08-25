<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-08-25 14:10:27
 * @FilePath: /php/laravel/app/Http/Controllers/Manga.php
 */

namespace App\Http\Controllers;

use App\Models\MangaSql;
use App\Models\UserSql;
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

        $mediaLimit = UserSql::get_media_limit($userId);

        if ($keyWord) {
            return MangaSql::manga_search($keyWord, $mediaLimit, '', $page, $pageSize, $userId);
        }

        if ($mediaId) {
            // 通过媒体库获取漫画
            return MangaSql::get($page, $pageSize, $mediaId, $mediaLimit, $userId);
        } else {
            // 获取全部漫画
            return MangaSql::get_nomedia($page, $pageSize, $mediaLimit);
        }
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

        return MangaSql::manga_update($mangaId, $data);
    }
    /**
     * @description: 移除漫画
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $mangaId = $request->post('mangaId');
        return MangaSql::manga_delete($mangaId);
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

        $tagidArr = explode(',', $tagIds);

        return MangaSql::get_by_tags($tagidArr, $page, $pageSize);
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

        return MangaSql::get_manga_info($mangaId, $userId);
    }
}
