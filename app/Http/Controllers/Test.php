<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-17 02:36:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-05-28 18:31:47
 * @FilePath: /php/laravel/app/Http/Controllers/Test.php
 */

namespace App\Http\Controllers;

use App\Jobs\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Test extends Controller
{
    public function get(){
        Scan::dispatch(123);//->onQueue('scan');
    }
    public function test(){
        // $res = DB::table('path')->where('','like','%path%')->get();
        $searchTerm = '/mnt/hhd-2t/00manga/00zip/123/456';

        // $results = DB::select(DB::raw("SELECT * FROM path WHERE path LIKE CONCAT('%', :searchTerm, '%')"), ['searchTerm' => $searchTerm]);

        $results = DB::table('path')->whereRaw(" ? LIKE CONCAT('%', path, '%')", [$searchTerm])->get();


        dump($results);
    }
}
