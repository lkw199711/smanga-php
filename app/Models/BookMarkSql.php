<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 13:40:56
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-26 06:15:51
 * @FilePath: \lar-demo\app\Models\BookMark.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookMarkSql extends Model
{
    use HasFactory;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'bookmark';
    protected $primaryKey = 'bookmarkId';
    protected $guarded = [];
    public $timestamps = true;

    const CREATED_AT = 'createTime';

    /**
     * 表中仅有createTime字段 重新setUpdatedAt方法置空操作 解决报错
     */
    public function setUpdatedAt($value)
    {
    }

    /**
     * @description: 获取用户书签列表-分页
     * @param {*} $userId
     * @param {*} $page
     * @return {*}
     */
    public static function get($userId, $page = 1, $pageSize = 10)
    {
        $paginate = self::join('chapter', 'chapter.chapterId', 'bookmark.chapterId')
            ->join('manga', 'manga.mangaId', 'bookmark.mangaId')
            ->where('userId', $userId)
            ->orderBy('createTime', 'desc')
            ->paginate($pageSize, ['*'], 'page', $page);

        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }

    /**
     * @description: 获取用户书签列表
     * @param {*} $userId
     * @return {*}
     */
    public static function get_nopage($userId)
    {
        $list = self::where('userId', $userId)->get();
        return new SqlList($list, count($list));
    }

    /**
     * @description: 获取全部书签
     * @param {*} $page
     * @return {*}
     */
    public static function all($page = 1, $pageSize = 10)
    {
        $paginate = self::paginate($pageSize, ['*'], 'page', $page);
        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }

    /**
     * @description: 新增书签
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle('书签新增错误', $e->getMessage());
        }
    }
    /**
     * @description: 移除删除
     * @param {*} $bookmarkId
     * @return {*}
     */
    public static function remove($bookmarkId)
    {
        try {
            return self::destroy($bookmarkId);
        } catch (\Exception $e) {
            return ErrorHandling::handle('书签删除错误', $e->getMessage());
        }
    }

    /**
     * @description: 根据漫画id删除
     * @param {*} $chapterId
     * @return {*}
     */
    public static function delete_by_mangaid($mangaId)
    {
        try {
            return self::where('mangaId', $mangaId)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle('书签删除错误', $e->getMessage());
        }
    }

    /**
     * @description: 根据章节id删除
     * @param {*} $chapterId
     * @return {*}
     */
    public static function delete_by_chapter($chapterId)
    {
        try {
            return self::where('chapterId', $chapterId)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle('书签删除错误', $e->getMessage());
        }
    }

    public static function fff($id)
    {
        // return self::find($id);
        return self::where('bookmarkId', $id)->first();
    }
}
