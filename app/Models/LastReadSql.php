<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-08 16:44:30
 * @FilePath: /php/laravel/app/Models/lastReadSql.php
 */

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\PublicClass\SqlList;
use App\Http\Controllers\ErrorHandling;

class LastReadSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'lastRead';
    protected $primaryKey = 'lastReadId';
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
     * @description: 格式化日期
     * @param {DateTimeInterface} $date
     * @return {*}
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
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
        $base = self::join('manga', 'manga.mangaId', 'lastRead.mangaId')->where('userId', $userId)->orderBy('lastRead.updateTime', 'desc');

        // 分页原型
        $paginate = $base->paginate($pageSize, ['*'], 'page', $page);

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
        $res = self::join('chapter', 'chapter.chapterId', 'lastRead.chapterId')
            ->where('userId', $userId)
            ->where('lastRead.mangaId', $mangaId)
            ->first();

        return $res;
    }

    /**
     * @description: 更新最终阅读记录
     * @param {*} $chapterId
     * @param {*} $data
     * @return {*}
     */
    public static function add($mangaId, $data)
    {
        try {
            return self::updateOrCreate(['mangaId' => $mangaId], $data);
        } catch (\Exception $e) {
            ErrorHandling::handle('更新阅读记录错误', $e->getMessage());
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
