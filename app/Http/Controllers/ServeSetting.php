<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\PublicClass\InterfacesResponse;

class ServeSettingResponse extends InterfacesResponse
{
    public string $interval;
    public int $autoCompress;
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

        $data = [
            'interval' => Utils::config_read('scan', 'interval'),
            'autoCompress' => Utils::config_read('scan', 'autoCompress')
        ];

        $res = new InterfacesResponse($data,'', 'get server setting success.');
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

        $response = new ServeSettingResponse();
        $response->message = '设置成功';
        $response->state = 'set server setting success.';
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

        $res = new InterfacesResponse(Utils::config_read('scan', 'interval'), '设置压缩选项成功', 'compress set success.');
        return new JsonResponse($res);
    }
    /**
     * @description: 压缩配置项获取
     * @return {*}
     */
    public function imagick_get()
    {
        $data = [
            'memory' => Utils::config_read('imagick', 'memory'),
            'map' => Utils::config_read('imagick', 'map'),
            'density' => Utils::config_read('imagick', 'density'),
            'quality' => Utils::config_read('imagick', 'quality'),
        ];

        $res = new InterfacesResponse($data, '', 'setting get success.');
        return new JsonResponse($res);
    }
    /**
     * @description: 获取守护进程项目
     * @return {*}
     */
    public function daemon_get()
    {

        $data = [
            Utils::config_read('daemon', 'time')
        ];

        $res = new InterfacesResponse($data, '', 'daemon get success.');
        return new JsonResponse($res);
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

        $data = [
            'time' => Utils::config_read('daemon', 'time')
        ];

        $res = new InterfacesResponse($data, '', 'daemon get success.');
        return new JsonResponse($res);
    }
}
