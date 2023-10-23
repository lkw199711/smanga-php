<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-10-23 20:18:26
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-23 20:22:38
 * @FilePath: /smanga-php/app/Http/PublicClass/ErrorResponse.php
 */

namespace App\Http\PublicClass;

use App\Http\PublicClass\InterfacesResponse;

class ErrorResponse extends InterfacesResponse
{
    public int $code = 1;
    public string $message = '系统错误';
    public string $errorLog;
    public string $errorCodeLog;

    public function __construct($message = '系统错误', $state = '', $errorCodeLog = '')
    {
        $this->message = $message;
        $this->state = $state;
        $this->errorLog = $message;
        $this->errorCodeLog = $errorCodeLog;
    }
}
