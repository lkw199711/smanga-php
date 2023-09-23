<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-09-23 11:55:31
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-09-23 12:03:14
 * @FilePath: /smanga-php/app/Http/Controllers/ErrorHandling.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use Illuminate\Http\JsonResponse;

class ErrorHandling extends Controller
{
    public static function handle($errorLog, $errorCodeLog)
    {
        return new JsonResponse(new ErrorResponse($errorLog, $errorCodeLog));
    }
}
