<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-08-26 03:53:44
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 00:06:24
 * @FilePath: /smanga-php/app/PublicClass/InterfacesResponse.php
 */

namespace App\Http\PublicClass;

use ReflectionClass;

/**
 * @description: 接口返回规范
 * @return {*}
 */
class InterfacesResponse
{
    public int $code = 0;
    public string $message = '';
    public string $state = '';
    public $request = '';

    public function __construct($request = null, $message = '', $state = '')
    {
        $this->request = $request;
        $this->message = $message;
        $this->state = $state;
    }

    public function to_array()
    {
        $reflection = new ReflectionClass($this);
        $data = [];

        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }

    public function json_response()
    {
        return json_encode($this);
    }
}
