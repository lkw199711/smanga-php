<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-08-26 03:45:57
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-08-26 04:23:33
 * @FilePath: /smanga-php/app/Http/PublicClass/ListRequest.php
 */
use App\Http\PublicClass\InterfacesRequest;

class ListRequest extends InterfacesRequest
{
    public array $list;
    public int $count;
}
