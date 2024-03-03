<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-26 13:48:20
 * @FilePath: /php/laravel/app/Http/Controllers/Path.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Jobs\Scan;
use App\Models\UserSql;
use App\Models\PathSql;
use App\Models\MangaSql;
use App\Models\ChapterSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Path extends Controller
{
    /**
     * @description: 获取路径信息
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        $userId = $request->post('userId');
        $mediaId = $request->post('mediaId');
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');

        $mediaLimit = UserSql::get_media_limit($userId);

        if ($mediaId) {
            // 通过媒体库获取漫画
            $sqlList = PathSql::get($mediaId, $page, $pageSize);
        } else {
            // 获取全部漫画
            $sqlList = PathSql::get_nomedia($mediaLimit, $page, $pageSize);
        }

        $res = new ListResponse($sqlList->list, $sqlList->count, '获取路径列表成功');

        return new JsonResponse($res);
    }

    /**
     * @description: 新增路径
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        $mediaId = $request->post('mediaId');
        $path = $request->post('path');
        $autoScan = $request->post('autoScan');
        $include = $request->post('include');
        $exclude = $request->post('exclude');

        if (!is_dir($path)) {
            $res = new ErrorResponse('路径无法读取');
            return new JsonResponse($res);
        }

        $pathInfo = PathSql::info($mediaId, $path);

        // 媒体库下有相同路径 返回错误
        if ($pathInfo) {
            $res = new ErrorResponse('路径已存在,请勿重复添加', 'path add filed');
            return new JsonResponse($res);
        }

        // 获取pathId
        $pathInfo = PathSql::add(['mediaId' => $mediaId, 'pathType'=>'main', 'path' => $path, 'autoScan' => $autoScan, 'include' => $include, 'exclude' => $exclude]);

        if (!$pathInfo) {
            $res = new ErrorResponse('路径添加错误', 'path add filed');
            return new JsonResponse($res);
        }

        // 添加扫描任务
        JobDispatch::handle('Scan', 'scan', $pathInfo->pathId);

        $res = new InterfacesResponse($pathInfo, '路径添加成功.', 'path add success');
        return new JsonResponse($res);
    }

    /**
     * @description: 删除路径
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $pathId = $request->post('pathId');

        JobDispatch::handle('DeletePath', 'delete', $pathId);
        $sqlRes = PathSql::path_delete($pathId);

        $res = new InterfacesResponse($sqlRes, '删除任务添加成功.', 'path add success');
        return new JsonResponse($res);
    }

    /**
     * @description: 扫描
     * @param {Request} $request
     * @return {*}
     */
    public function scan(Request $request)
    {
        $pathId = $request->post('pathId');

        JobDispatch::handle('Scan', 'scan', $pathId);

        $res = new InterfacesResponse('', '扫描任务添加成功.', 'path-scan add success');
        return new JsonResponse($res);
    }
    /**
     * @description: 重新扫描
     * @param {Request} $request
     * @return {*}
     */
    public function rescan(Request $request)
    {
        $pathId = $request->post('pathId');

        JobDispatch::handle('DeletePath', 'delete', $pathId, true);
        JobDispatch::handle('Scan', 'scan', $pathId);

        $res = new InterfacesResponse('', '重新扫描任务添加成功.', 'path-rescan add success');
        return new JsonResponse($res);
    }
}
