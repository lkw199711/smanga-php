<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-08-26 03:53:44
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-08-26 04:21:15
 * @FilePath: /smanga-php/app/PublicClass/InterfacesRequest.php
 */
namespace App\Http\PublicClass;
/**
 * @description: 接口返回规范
 * @return {*}
 */
class InterfacesRequest
{
    public int $code=0;
    public string $message;
    public string $status;
    public $request;
}
