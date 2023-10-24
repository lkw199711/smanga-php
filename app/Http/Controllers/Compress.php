<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-16 03:04:27
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 19:51:53
 * @FilePath: /php/laravel/app/Http/Controllers/Compress.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Models\UserSql;
use App\Models\CompressSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Compress extends Controller
{
    /**
     * @description: 获取转换列表
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        // 接受参数
        $userId = $request->post('userId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');

        $mediaLimit = UserSql::get_media_limit($userId);

        $sqlList = CompressSql::get($mediaLimit, $page, $pageSize);
        $res = new ListResponse($sqlList->list, $sqlList->count, '获取解压列表成功.');
        return new JsonResponse($res);
    }
    /**
     * @description: 新增转换记录
     * @return {*}
     */
    public function add()
    {
        $data = [];
        $compressInfo = CompressSql::add($data);
        $res = new InterfacesResponse($compressInfo, '解压记录添加成功.', 'comporess add success.');
        return new JsonResponse($res);
    }
    /**
     * @description: 修改转换记录
     * @return {*}
     */
    public function update(Request $request)
    {
        $chapterId = $request->post('chapterId');
        $data = [];
        $sqlRes = CompressSql::compress_update($chapterId, $data);
        $res = new InterfacesResponse($sqlRes, '解压记录添加成功.', 'comporess add success.');
        return new JsonResponse($res);
    }
    /**
     * @description: 删除转换记录
     * @param {*} $compressId
     * @return {*}
     */
    public function delete(Request $request)
    {
        $compressId = $request->post('compressId');
        $sqlRes = CompressSql::compress_delete($compressId);
        $res = new InterfacesResponse($sqlRes, '解压记录添加成功.', 'comporess add success.');
        return new JsonResponse($res);
    }
}
