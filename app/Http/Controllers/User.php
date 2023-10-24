<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-14 16:46:50
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 22:52:10
 * @FilePath: /php/laravel/app/Http/Controllers/User.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\PublicClass\ListResponse;
use App\Http\PublicClass\SqlList;
use App\Models\UserSql;
use App\Models\LoginSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class User extends Controller
{
    /**
     * @description: 登录
     * @param {Request} $request
     * @return {*}
     */
    public function login(Request $request)
    {
        $userName = $request->post('userName');
        $passWord = $request->post('passWord');
        $ip = $_SERVER['REMOTE_ADDR'];
        $passMd5 = md5($passWord);

        $userInfo = UserSql::get_info_by_name($userName);

        if ($userInfo) {
            if (strtolower($userInfo->passWord) === strtolower($passMd5)) {
                // 登录成功
                $data = ['userId' => $userInfo->userId, 'userName' => $userName, 'nickName' => $userInfo->nickName, 'request' => 0, 'ip' => $ip];
                // 记录登录表
                LoginSql::add($data);
                // 返回信息
                $res = new InterfacesResponse($userInfo, '登陆成功', 'login success.');
                return new JsonResponse($res);
            } else {
                // 密码错误
                $data = ['userId' => $userInfo->userId, 'userName' => $userName, 'nickName' => $userInfo->nickName, 'request' => 1, 'ip' => $ip];
                // 记录登录表
                LoginSql::add($data);
                // 返回信息
                $res = new ErrorResponse('密码错误', 'password error.');
                return new JsonResponse($res);
            }
        } else {
            // 用户名不存在
            $data = ['request' => 2, 'ip' => $ip];
            LoginSql::add($data);

            $res = new ErrorResponse('用户名不存在', 'user not find.');
            return new JsonResponse($res);
        }
    }
    /**
     * @description: 获取用户列表
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        $page = $request->post('page');
        $pageSize = $request->post('pageSize');
        $sqlList = UserSql::get($page, $pageSize);

        $res = new ListResponse($sqlList->list, $sqlList->count, '用户列表获取成功.');
        return new JsonResponse($res);
    }
    /**
     * @description: 新增用户
     * @param {Request} $request
     * @return {*}
     */
    public function add(Request $request)
    {
        $userName = $request->post('userName');
        $passWord = $request->post('passWord');
        $passMd5 = md5($passWord);

        $userInfo = UserSql::get_info_by_name($userName);

        if ($userInfo) {
            $res = new ErrorResponse('此用户名已被注册', 'This username has already been registered');
        } else {
            $data = ['userName' => $userName, 'passWord' => $passMd5];
            $userInfo = UserSql::add($data);
            $res = new InterfacesResponse($userInfo, '用户新增成功.', 'user add success.');
        }

        return new JsonResponse($res);
    }
    /**
     * @description: 修改用户信息
     * @param {Request} $request
     * @return {*}
     */
    public function update(Request $request)
    {
        $targetUserId = $request->post('targetUserId');
        $userName = $request->post('userName');
        $passWord = $request->post('passWord');
        $editUser = $request->post('editUser');
        $editMedia = $request->post('editMedia');
        $mediaLimit = $request->post('mediaLimit');
        $passMd5 = md5($passWord);

        $data = ['userName' => $userName, 'editUser' => $editUser, 'editMedia' => $editMedia, 'mediaLimit' => $mediaLimit];

        // 有设置密码则变更密码
        if ($passWord) {
            $data['passWord'] = $passMd5;
        }

        $sqlRes = UserSql::user_update($targetUserId, $data);

        $res = new InterfacesResponse($sqlRes, '用户信息修改成功.', 'user data update success.');
        return new JsonResponse($res);
    }
    /**
     * @description: 删除用户
     * @param {Request} $request
     * @return {*}
     */
    public function delete(Request $request)
    {
        $targetUserId = $request->post('targetUserId');

        $sqlRes = UserSql::user_delete($targetUserId);

        $res = new InterfacesResponse($sqlRes, '用户删除成功.', 'user delete success.');
        return new JsonResponse($res);
    }
}
