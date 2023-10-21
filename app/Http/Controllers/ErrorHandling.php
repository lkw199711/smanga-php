<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-09-23 11:55:31
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-22 02:32:30
 * @FilePath: /smanga-php/app/Http/Controllers/ErrorHandling.php
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Models\LogSql;
use Illuminate\Http\JsonResponse;

class ErrorHandling extends Controller
{
    public static function handle($errorLog, $errorCodeLog = '')
    {
        // 记录错误日志
        LogSql::add([
            'logType' => 'error',
            'logLevel' => 3,
            'logContent' => $errorLog,
        ]);

        return new JsonResponse(new ErrorResponse($errorLog, $errorCodeLog));
    }
}
