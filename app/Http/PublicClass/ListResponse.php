<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-08-26 03:45:57
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-09-23 12:55:18
 * @FilePath: /smanga-php/app/Http/PublicClass/ListResponse.php
 */

namespace App\Http\PublicClass;

use App\Http\PublicClass\InterfacesResponse;

/**
 * @description: 返给前端用列表格式
 * @return {*}
 */
class ListResponse extends InterfacesResponse
{
    public $list;
    public int $count;
    public string $state;

    public function __construct($list, $count, $state = '列表获取成功')
    {
        $this->list = $list;
        $this->count = $count;
        $this->state = $state;
    }
}
