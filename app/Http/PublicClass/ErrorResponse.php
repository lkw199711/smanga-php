<?php

namespace App\Http\PublicClass;

use App\Http\PublicClass\InterfacesResponse;

class ErrorResponse extends InterfacesResponse
{
    public int $code = 1;
    public string $message = '系统错误';
    public string $errorLog;
    public string $errorCodeLog;

    public function __construct($errorLog = '系统错误', $errorCodeLog = '')
    {
        $this->errorLog = $errorLog;
        $this->errorCodeLog = $errorCodeLog;
    }
}
