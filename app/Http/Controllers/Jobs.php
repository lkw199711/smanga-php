<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-16 03:04:27
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-19 02:07:20
 * @FilePath: /php/laravel/app/Http/Controllers/Compress.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Models\JobsSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Jobs extends Controller
{
    /**
     * @description: 获取转换列表
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        // 接受参数
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');

        $sqlList = JobsSql::get($page, $pageSize);
        $res = new ListResponse($sqlList->list, $sqlList->count, '获取任务列表成功.');
        return new JsonResponse($res);
    }

    /**
     * @description: 删除转换记录
     * @param {*} $compressId
     * @return {*}
     */
    public function delete(Request $request)
    {
        $jobsId = $request->post('jobId');
        $sqlRes = JobsSql::jobs_delete($jobsId);
        $res = new InterfacesResponse($sqlRes, '任务删除成功.', 'job delete success.');
        return new JsonResponse($res);
    }
}
