<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-20 11:22:23
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-08-26 08:00:46
 * @FilePath: /php/laravel/app/Http/Middleware/DeployStatusCheck.php
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils;

class DeployStatusCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $configPath = Utils::get_env('SMANGA_CONFIG');
        $installLock = "$configPath/install.lock";

        if (!is_file($installLock)) {
            return ['code' => 2, 'status' => 'first deploy'];
        }

        return $next($request);
    }
}
