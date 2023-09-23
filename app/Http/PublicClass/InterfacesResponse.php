<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-08-26 03:53:44
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-09-23 13:45:35
 * @FilePath: /smanga-php/app/PublicClass/InterfacesResponse.php
 */

namespace App\Http\PublicClass;

/**
 * @description: 接口返回规范
 * @return {*}
 */
class InterfacesResponse
{
    public int $code = 0;
    public string $message;
    public string $state;
    public $request;

    public function __construct($request = null, $message = '操作成功', $state = '')
    {
        $this->request = $request;
        $this->message = $message;
        $this->state = $state;
    }
}
