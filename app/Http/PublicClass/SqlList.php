<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-09-23 12:55:01
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-09-23 12:55:02
 * @FilePath: /smanga-php/app/Http/PublicClass/SqlList.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace App\Http\PublicClass;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class SqlList
{
    public $list;
    public int $count;

    public function __construct($list, $count)
    {
        $this->list = $list;
        $this->count = $count;
    }
}
