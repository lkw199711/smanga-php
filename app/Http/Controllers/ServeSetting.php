<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServeSetting extends Controller
{
    /**
     * @description: 获取用户配置信息
     * @param {Request} $request
     * @return {*}
     */
    public function get(Request $request)
    {
        $userId = $request->post('userId');
        return Utils::config_read('sql', '');
    }

    /**
     * @description: 扫描项设置
     * @param {Request} $request
     * @return {*}
     */
    public function scan_set(Request $request)
    {
        $interval = $request->post('interval');

        Utils::config_write('scan', 'interval', $interval);

        return [
            'code' => 0,
            'message' => '设置扫描选项成功',
            'data' => [
                'interval' => Utils::config_read('scan', 'interval')
            ]
        ];
    }
    /**
     * @description: 扫描项获取
     * @return {*}
     */
    public function scan_get()
    {
        return [
            'code' => 0,
            'request' => '获取扫描设置成功',
            'data' => [
                'interval' => Utils::config_read('scan', 'interval')
            ]
        ];
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
    public function daemon_set(Request $request){
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
