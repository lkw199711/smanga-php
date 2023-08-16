<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @LastEditTime: 2023-08-17 00:43:55
 * @FilePath: /php/laravel/app/Models/HistorySql.php
 */

namespace App\Models;

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
        $res = self
            ::join('manga as m', 'history.mangaId', 'm.mangaId')
            ->join('chapter as c', 'history.chapterId', 'c.chapterId')
            ->select(DB::raw('max(history.createTime) as nearTime'))
            ->addSelect('history.historyId')
            ->addSelect('m.mangaId', 'm.mangaName', 'm.mangaCover', 'm.browseType', 'm.direction', 'm.removeFirst')
            ->addSelect('c.chapterId', 'c.chapterName', 'c.chapterPath', 'c.chapterType')
            ->where('history.userId', $userId)
            ->groupBy('history.chapterId')
            ->orderBy('nearTime')
            ->paginate($pageSize, ['*'], 'page', $page);

        return ['code' => 0, 'text' => '获取历史记录成功', 'list' => $res];
    }

    /**
     * @description: 获取最后一次阅读记录
     * @param {*} $mangaId
     * @param {*} $userId
     * @return {*}
     */
    public static function get_latest($mangaId, $userId)
    {
        $res = self
            ::join('chapter as c', 'history.chapterId', 'c.chapterId')
            ->addSelect('history.historyId', 'history.mangaId', 'history.userId')
            ->addSelect('c.chapterId', 'c.chapterName', 'c.chapterPath', 'c.chapterType', 'c.chapterCover', 'c.browseType')
            ->where('history.mangaId', $mangaId)
            ->where('history.userId', $userId)
            ->orderBy('history.createTime')
            ->first();

        // 根据是否获取到数据生产状态码
        $code = $res ? 0 : 1;


        return ['code' => $code, 'text' => '获取历史记录成功', 'info' => $res];
    }

    /**
     * @description: 添加历史记录
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return ['code' => 0, 'text' => '添加成功', 'request' => self::create($data)];
        } catch (\Exception $e) {
            return ['code' => 1, 'text' => '系统错误', 'eMsg' => $e->getMessage()];
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
            return ['code' => 0, 'message' => '删除成功', 'request' => self::where('historyId', $historyId)->delete()];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
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
            return ['code' => 0, 'message' => '删除成功', 'request' => self::where('mangaId', $mangaId)->delete()];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
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
            return ['code' => 0, 'message' => '删除成功', 'request' => self::where('chapterId', $chapterId)->delete()];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
}
