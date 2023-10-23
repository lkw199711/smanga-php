<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 00:18:39
 * @FilePath: /php/laravel/app/Http/Controllers/Config.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Models\ConfigSql;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class Config extends Controller
{
    /**
     * @description: 获取用户配置信息
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        $userId = $request->post('userId');

        $configInfo = ConfigSql::get($userId);

        if ($configInfo) {
            $res = new InterfacesResponse($configInfo->configValue, '', 'get user-config success.');
        } else {
            $res = new ErrorResponse('', 'no user-config.');
        }

        return new JsonResponse($res);
    }
    /**
     * @description: 设置用户配置
     * @param {Request} $request
     * @return {*}
     */
    public function set(Request $request)
    {
        $userId = $request->post('userId');
        $userName = $request->post('userName');
        $configValue = $request->post('configValue');

        $configInfo = ConfigSql::set($userId, ['userId' => $userId, 'userName' => $userName, 'configValue' => $configValue]);

        $res = new InterfacesResponse($configInfo->configValue, '', 'get user-config success.');
        return new JsonResponse($res);
    }
}
