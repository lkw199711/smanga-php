<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-28 04:08:10
 * @FilePath: /php/laravel/app/Http/Controllers/Tag.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Models\MangaTagSql;
use App\Models\TagSql;
use App\Models\UserSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Tag extends Controller
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
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');

        $nopage = $request->post('nopage');

        if ($nopage) {
            $sqlList = TagSql::get_no_page($userId);
        } else {
            $sqlList = TagSql::get($userId, $page, $pageSize);
        }


        $res = new ListResponse($sqlList->list, $sqlList->count, '标签列表获取成功.');
        return new JsonResponse($res);
    }

    /**
     * @description: 新增标签
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $tagName = $request->post('tagName');
        $tagColor = $request->post('tagColor');
        $description = $request->post('description');

        if (!$tagColor) {
            $tagColor = '#a0d911';
        }

        $data = [
            'userId' => $userId,
            'tagName' => $tagName,
            'tagColor' => $tagColor,
            'description' => $description,
        ];

        $tagInfo = TagSql::add($data);

        $res = new InterfacesResponse($tagInfo, '标签新增成功.', 'tag add success.');
        return new JsonResponse($res);
    }

    /**
     * @description: 修改标签信息
     * @param {Request} $request
     * @return {*}
     */
    public function update(Request $request)
    {
        $tagId = $request->post('tagId');
        $tagName = $request->post('tagName');
        $tagColor = $request->post('tagColor');
        $description = $request->post('description');

        $data = ['tagName' => $tagName, 'tagColor' => $tagColor, 'description' => $description];
        $sqlRes = TagSql::tag_update($tagId, $data);

        $res = new InterfacesResponse($sqlRes, '标签修改成功', 'tag update success.');
        return new JsonResponse($res);
    }

    /**
     * @description: 移除标签
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $tagId = $request->post('tagId');
        $sqlRes = TagSql::tag_delete($tagId);

        $res = new InterfacesResponse($sqlRes, '标签删除成功', 'tag update success.');
        return new JsonResponse($res);
    }

    /**
     * @description: 新增漫画标签
     * @param {Request} $request
     * @return {*}
     */
    public function add_manga_tags(Request $request)
    {
        $userId = $request->post('userId');
        $tagIds = $request->post('tagIds');
        $mangaId = $request->post('mangaId');

        // id组通过字符串方式发送过来,通过拆分逗号获取正式数据
        $tagIdArr = explode(',', $tagIds);

        // 先删除与漫画相关的所有记录,避免重复
        MangaTagSql::delete_by_mangaId($userId, $mangaId);

        foreach ($tagIdArr as $tagId) {
            MangaTagSql::add(['tagId' => $tagId, 'mangaId' => $mangaId, 'userId' => $userId]);
        }

        $res = new InterfacesResponse('', '标签修改成功', 'Manga tag edit successful.');
        return new JsonResponse($res);
    }

    /**
     * @description: 获取漫画标签
     * @param {Request} $request
     * @return {*}
     */
    public function get_manga_tag(Request $request)
    {
        $userId = $request->post('userId');
        $mangaId = $request->post('mangaId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');
        $nopage = $request->post('nopage');

        if ($nopage) {
            $sqlList = MangaTagSql::get_nopage($userId, $mangaId);
        }

        $sqlList = MangaTagSql::get($userId, $mangaId, $page, $pageSize);

        $res = new ListResponse($sqlList->list, $sqlList->count, '标签列表获取成功.');
        return new JsonResponse($res);
    }

    /**
     * @description: 新增漫画标签
     * @param {Request} $request
     * @return {*}
     */
    public function add_manga_tag(Request $request)
    {
        $tagId = $request->post('tagId');
        $mangaId = $request->post('mangaId');

        $sqlRes = MangaTagSql::add(['tagId' => $tagId, 'mangaId' => $mangaId]);
        $res = new InterfacesResponse($sqlRes, '漫画标签新增成功.', 'tag update success.');
        return new JsonResponse($res);
    }

    /**
     * @description: 移除漫画标签
     * @param {Request} $request
     * @return {*}
     */
    public function remove_manga_tag(Request $request)
    {
        $mangaTagId = $request->post('mangaTagId');

        $sqlRes = MangaTagSql::manga_tag_delete($mangaTagId);
        $res = new InterfacesResponse($sqlRes, '漫画标签移除成功.', 'tag update success.');
        return new JsonResponse($res);
    }
}
