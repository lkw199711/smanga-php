<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\PublicClass\InterfacesRequest;

class ServeSettingRequest extends InterfacesRequest
{
    public $interval;
    public $autoCompress;
}

class ServeSetting extends Controller
{
    /**
     * @description: 获取用户配置信息
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        // 验证用户是否有权限进行服务器设置 (待完成)
        $userId = $request->post('userId');

        $interval = Utils::config_read('scan', 'interval');
        $autoCompress = Utils::config_read('scan', 'autoCompress');

        $res = new ServeSettingRequest();
        $res->status = 'get server setting success.';
        $res->interval = $interval;
        $res->autoCompress = $autoCompress;

        return new JsonResponse($res);
    }

    /**
     * @description: 设置服务器配置项
     * @param {Request} $request
     * @return {*}
     */
    public function set(Request $request)
    {
        $title = $request->post('title');
        $key = $request->post('key');
        $value = $request->post('value');

        Utils::config_write($title, $key, $value);

        $response = new ServeSettingRequest();
        $response->message = '设置成功';
        $response->status = 'set server setting success.';
        $response->$key = Utils::config_read($title, $key);

        return new JsonResponse($response);
    }

    /**
     * @description: 压缩配置项设置
     * @param {Request} $request
     * @return {*}
     */
    public function imagick_set(Request $request)
    {
        $memory = $request->post('memory');
        $map = $request->post('map');
        $density = $request->post('density');
        $quality = $request->post('quality');

        Utils::config_write('imagick', 'memory', $memory);
        Utils::config_write('imagick', 'map', $map);
        Utils::config_write('imagick', 'density', $density);
        Utils::config_write('imagick', 'quality', $quality);

        return [
            'code' => 0,
            'message' => '设置压缩选项成功',
            'data' => [
                'interval' => Utils::config_read('scan', 'interval')
            ]

        ];
    }
    /**
     * @description: 压缩配置项获取
     * @return {*}
     */
    public function imagick_get()
    {
        return [
            'code' => 0,
            'request' => '获取压缩设置成功',
            'data' => [
                'memory' => Utils::config_read('imagick', 'memory'),
                'map' => Utils::config_read('imagick', 'map'),
                'density' => Utils::config_read('imagick', 'density'),
                'quality' => Utils::config_read('imagick', 'quality'),
            ]
        ];
    }
    /**
     * @description: 获取守护进程项目
     * @return {*}
     */
    public function daemon_get()
    {
        return [
            'code' => 0,
            'request' => '获取守护进程设置项成功',
            'data' => [
                'time' => Utils::config_read('daemon', 'time')
            ]
        ];
    }
    /**
     * @description: 设置守护进程项目
     * @param {Request} $request
     * @return {*}
     */
    public function daemon_set(Request $request)
    {
        $time = $request->post('time');

        Utils::config_write('daemon', 'time', $time);

        return [
            'code' => 0,
            'message' => '设置守护进程选项成功',
            'data' => [
                'time' => Utils::config_read('daemon', 'time')
            ]

        ];
    }
}
