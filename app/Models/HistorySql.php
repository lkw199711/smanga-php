<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-26 04:41:51
 * @FilePath: /php/laravel/app/Models/HistorySql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HistorySql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'history';
    protected $primaryKey = 'historyId';
    protected $guarded = [];
    public $timestamps = true;

    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';
    /**
     * 表中仅有createTime字段 重新setUpdatedAt方法置空操作 解决报错
     */
    public function setUpdatedAt($value)
    {
    }

    /**
     * @description: 获取历史记录
     * @param {*} $userId
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function get($userId, $page, $pageSize)
    {
        $paginate = self
            ::join('manga as m', 'history.mangaId', 'm.mangaId')
            ->join('chapter as c', 'history.chapterId', 'c.chapterId')
            ->select(DB::raw('max(history.createTime) as nearTime'))
            ->addSelect('history.historyId')
            ->addSelect('m.mangaId', 'm.mangaName', 'm.mangaCover', 'm.browseType', 'm.direction', 'm.removeFirst')
            ->addSelect('c.chapterId', 'c.chapterName', 'c.chapterCover', 'c.chapterPath', 'c.chapterType')
            ->where('history.userId', $userId)
            ->groupBy('history.chapterId')
            ->orderBy('nearTime')
            ->paginate($pageSize, ['*'], 'page', $page);

        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }

    /**
     * @description: 获取最后一次阅读记录
     * @param {*} $mangaId
     * @param {*} $userId
     * @return {*}
     */
    public static function get_latest($mangaId, $userId)
    {
        $first = self
            ::join('chapter as c', 'history.chapterId', 'c.chapterId')
            ->addSelect('history.historyId', 'history.mangaId', 'history.userId')
            ->addSelect('c.chapterId', 'c.chapterName', 'c.chapterPath', 'c.chapterType', 'c.chapterCover', 'c.browseType')
            ->where('history.mangaId', $mangaId)
            ->where('history.userId', $userId)
            ->orderBy('history.createTime')
            ->first();

        return $first;
    }

    /**
     * @description: 添加历史记录
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("历史记录添加失败.", $e->getMessage());
        }
    }

    /**
     * @description: 获取历史记录信息
     * @param {*} $historyId
     * @return {*}
     */
    public static function info($historyId)
    {
        return self::where('historyId', $historyId)->first();
    }

    /**
     * @description: 删除历史记录
     * @param {*} $historyId
     * @return {*}
     */
    public static function remove($historyId)
    {
        try {
            return self::where('historyId', $historyId)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("历史记录删除失败.", $e->getMessage());
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
            return ErrorHandling::handle("历史记录删除失败.", $e->getMessage());
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
            return ErrorHandling::handle("历史记录删除失败.", $e->getMessage());
        }
    }
}
