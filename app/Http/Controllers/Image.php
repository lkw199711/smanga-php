<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-16 22:28:29
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-25 01:36:17
 * @FilePath: /php/laravel/app/Http/Controllers/Image.php
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\LastRead;
use App\Http\PublicClass\ErrorResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Image extends Controller
{
    public function get(Request $request)
    {
        $file = $request->post('file');

        if (!is_file($file)) {
            $res = new ErrorResponse('图片路径错误', 'image error.');
            return new JsonResponse($res);
        }

        // 图片文件的路径
        // $imagePath = public_path('images/sample.jpg'); // 这里假设图片存储在public目录中
        $imagePath = $file; // 这里假设图片存储在public目录中

        // 设置文件的MIME类型，这里假设你要返回JPEG图片
        $headers = [
            'Content-Type' => 'image/jpeg',
        ];

        // 使用StreamedResponse返回图片文件流
        return new StreamedResponse(function () use ($imagePath) {
            $fileStream = fopen($imagePath, 'rb');

            // 输出图片文件流
            while (!feof($fileStream)) {
                echo fread($fileStream, 1024);
                flush();
            }

            fclose($fileStream);
        }, 200, $headers);

        // return file_get_contents($file);
    }
}
