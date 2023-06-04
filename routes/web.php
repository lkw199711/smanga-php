<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-04 20:56:21
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-05-29 09:31:24
 * @FilePath: \lar-demo\routes\web.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\Ding;
use Illuminate\Http\Request;

Route::get('d1', [Ding::class, 'test']);
Route::get('d2', [Ding::class, 'dbtest']);
Route::post('d3', [Ding::class, 'get_bookmark']);
// 书签
Route::post('bookmark/get', [App\Http\Controllers\BookMark::class, 'get']);
Route::post('bookmark/add', [App\Http\Controllers\BookMark::class, 'add']);
Route::post('bookmark/remove', [App\Http\Controllers\BookMark::class, 'remove']);
// 收藏
Route::post('collect/get', [App\Http\Controllers\Collect::class, 'get']);
Route::post('collect/add', [App\Http\Controllers\Collect::class, 'add']);
Route::post('collect/remove', [App\Http\Controllers\Collect::class, 'remove']);
Route::post('collect/is-collect', [App\Http\Controllers\Collect::class, 'is_collect']);
// 用户
Route::post('user/config/get', [App\Http\Controllers\Config::class, 'get']);
Route::post('user/config/set', [App\Http\Controllers\Config::class, 'set']);

Route::post('user/get', [App\Http\Controllers\User::class, 'get']);
Route::post('user/register', [App\Http\Controllers\User::class, 'add']);
Route::post('user/update', [App\Http\Controllers\User::class, 'update']);
Route::post('user/delete', [App\Http\Controllers\User::class, 'delete']);
// 历史记录
Route::post('history/get', [App\Http\Controllers\History::class, 'get']);
Route::post('history/add', [App\Http\Controllers\History::class, 'add']);
Route::post('history/delete', [App\Http\Controllers\History::class, 'delete']);
// 媒体库
Route::post('media/get', [App\Http\Controllers\Media::class, 'get']);
Route::post('media/add', [App\Http\Controllers\Media::class, 'add']);
Route::post('media/update', [App\Http\Controllers\Media::class, 'update']);
Route::post('media/delete', [App\Http\Controllers\Media::class, 'delete']);
// 漫画
Route::post('manga/get', [App\Http\Controllers\Manga::class, 'get']);
Route::post('manga/add', [App\Http\Controllers\Manga::class, 'add']);
Route::post('manga/update', [App\Http\Controllers\Manga::class, 'update']);
Route::post('manga/delete', [App\Http\Controllers\Manga::class, 'delete']);
// 章节
Route::post('chapter/get', [App\Http\Controllers\Chapter::class, 'get']);
Route::post('chapter/add', [App\Http\Controllers\Chapter::class, 'add']);
Route::post('chapter/update', [App\Http\Controllers\Chapter::class, 'update']);
Route::post('chapter/delete', [App\Http\Controllers\Chapter::class, 'delete']);
// 版本
Route::post('version/get', [App\Http\Controllers\Version::class, 'get']);
// 路径
Route::post('path/get', [App\Http\Controllers\Path::class, 'get']);
Route::post('path/add', [App\Http\Controllers\Path::class, 'add']);
Route::post('path/delete', [App\Http\Controllers\Path::class, 'delete']);
Route::post('path/scan', [App\Http\Controllers\Path::class, 'scan']);
// 转换
Route::post('compress/get', [App\Http\Controllers\Compress::class, 'get']);
Route::post('compress/add', [App\Http\Controllers\Compress::class, 'add']);
Route::post('compress/update', [App\Http\Controllers\Compress::class, 'update']);
Route::post('compress/delete', [App\Http\Controllers\Compress::class, 'delete']);
// 搜索
Route::post('search/get', [App\Http\Controllers\Search::class, 'get']);
// 图片
Route::post('image/get', [App\Http\Controllers\Image::class, 'get']);


Route::any('test', function (Request $request) {
    $pathId = $request->post('pathId');
    App\Jobs\Scan::dispatch($pathId);
});

Route::post('path/scan', [App\Http\Controllers\Path::class, 'scan']);
Route::post('path/rescan', [App\Http\Controllers\Path::class, 'rescan']);
Route::post('chapter/image', [App\Http\Controllers\Chapter::class, 'image_list']);
