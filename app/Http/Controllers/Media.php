<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 00:20:27
 * @FilePath: /php/app/Http/Controllers/Media.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Models\MediaSql;
use App\Models\UserSql;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class Media extends Controller
{
    /**
     * @description: 获取媒体库列表
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

        if ($mediaLimit) {
            $sqlList = MediaSql::get($page, $pageSize, $mediaLimit);
        } else {
            $sqlList = MediaSql::get_nolimit($page, $pageSize);
        }

        $res = new ListResponse($sqlList->list, $sqlList->count, '获取媒体库列表成功.');
        return new JsonResponse($res);
    }
    /**
     * @description: 新增媒体库
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        $mediaName = $request->post('mediaName');
        $mediaType = $request->post('mediaType');
        $directoryFormat = $request->post('directoryFormat');
        $fileType = $request->post('fileType');
        $defaultBrowse = $request->post('defaultBrowse');
        $removeFirst = $request->post('removeFirst');
        $direction = $request->post('direction');
        $autoScan = $request->post('autoScan');

        $data = [
            'mediaName' => $mediaName, 'mediaType' => $mediaType, 'directoryFormat' => $directoryFormat,
            'fileType' => $fileType, 'defaultBrowse' => $defaultBrowse, 'removeFirst' => $removeFirst, 'direction' => $direction,
        ];

        $mediaInfo = MediaSql::add($data);

        if (!$mediaInfo) {
            $res = new ErrorResponse('媒体库添加失败.', 'media add filed.');
            return new JsonResponse($res);
        }

        $res = new InterfacesResponse($mediaInfo, '媒体库添加成功.', 'path add success.');
        return new JsonResponse($res);
    }

    /**
     * @description: 修改媒体库信息
     * @param {Request} $request
     * @return {*}
     */
    public function update(Request $request)
    {
        $mediaId = $request->post('mediaId');
        $mediaName = $request->post('mediaName');
        $mediaType = $request->post('mediaType');
        $directoryFormat = $request->post('directoryFormat');
        $fileType = $request->post('fileType');
        $defaultBrowse = $request->post('defaultBrowse');
        $removeFirst = $request->post('removeFirst');
        $direction = $request->post('direction');

        $data = [
            'mediaName' => $mediaName, 'mediaType' => $mediaType, 'directoryFormat' => $directoryFormat,
            'fileType' => $fileType, 'defaultBrowse' => $defaultBrowse, 'removeFirst' => $removeFirst, 'direction' => $direction
        ];

        $sqlRes = MediaSql::media_update($mediaId, $data);

        $res = new InterfacesResponse($sqlRes, '媒体库修改成功.', 'path update success.');
        return new JsonResponse($res);
    }

    /**
     * @description: 删除媒体库
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $mediaId = $request->post('mediaId');

        JobDispatch::handle('DeleteMedia', 'delete', $mediaId);
        $sqlRes = MediaSql::media_delete($mediaId);

        $res = new InterfacesResponse($sqlRes, '媒体库删除成功.', 'path delete success.');
        return new JsonResponse($res);
    }
}
