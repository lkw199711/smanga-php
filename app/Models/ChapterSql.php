<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-09-24 23:13:49
 * @FilePath: \lar-demo\app\Models\Chapter.php
 */

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChapterSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'chapter';
    protected $primaryKey = 'chapterId';
    protected $guarded = [];
    protected $hidden = [];
    public $timestamps = true;

    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

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
     * @description: 获取漫画章节列表-分页
     * @param {*} $userId
     * @param {*} $page
     * @return {*}
     */
    public static function get($mangaId, $page, $pageSize, $order)
    {
        $orderText = self::get_order_text($order);

        $sql = self::where('mangaId', $mangaId)
            ->orderByRaw($orderText);

        $res = $sql
            ->paginate($pageSize, ['*'], 'page', $page);

        $toSql = $sql->toSql();

        return ['code' => 0, 'request' => '获取成功', 'list' => $res];
    }

    /**
     * @description: 不分页获取
     * @param {*} $mangaId
     * @param {*} $order
     * @return {*}
     */
    public static function get_nopage($mangaId, $order)
    {
        $orderText = self::get_order_text($order);

        $res = self::where('mangaId', $mangaId)
            ->orderByRaw($orderText)
            ->get();

        $toSql = self::where('mangaId', $mangaId)
            ->orderByRaw($orderText)
            ->toSql();
        return ['code' => 0, 'request' => '获取成功', 'list' => new RequestList($res, count($res)), 'toSql' => $toSql];
    }

    /**
     * @description: 获取全部章节-管理用
     * @param {*} $mediaLimit
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function get_nomanga($mediaLimit, $page = 1, $pageSize = 10)
    {
        $res = self::whereNotIn('mediaId', $mediaLimit)->paginate($pageSize, ['*'], 'page', $page);
        return ['code' => 0, 'request' => '获取成功', 'list' => $res];
    }

    /**
     * @description: 获取此漫画的开篇章节
     * @param {*} $mangaId
     * @return {*}
     */
    public static function get_first($mangaId)
    {
        $orderText = self::get_order_text('name');
        $res = self::where('mangaId', $mangaId)->orderByRaw($orderText)->first();

        return ['code' => 0, 'request' => '获取成功', 'info' => $res];
    }
    /**
     * @description: 新增章节
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
    /**
     * @description: 搜索章节
     * @param {*} $keyWord
     * @param {*} $mediaLimit
     * @param {*} $order
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function chapter_search($keyWord, $mediaLimit, $order, $page, $pageSize)
    {
        $orderText = self::get_order_text($order);

        $base = self::whereNotIn('mediaId', $mediaLimit)
            ->where('chapterName', 'like', "%{$keyWord}%")
            ->orderByRaw($orderText);

        // $sql = $base->toSql();

        $res = $base->paginate($pageSize, ['*'], 'page', $page);

        return ['code' => 0, 'request' => '获取成功', 'list' => $res];
    }
    /**
     * @description: 修改章节信息
     * @param {*} $chapterId
     * @param {*} $data
     * @return {*}
     */
    public static function chapter_update($chapterId, $data)
    {
        try {
            return ['code' => 0, 'message' => '修改成功', 'request' => self::where('chapterId', $chapterId)->update($data)];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
    /**
     * @description: 删除章节记录
     * @param {*} $chapterId
     * @return {*}
     */
    public static function chapter_delete($chapterId, $deep = true)
    {
        try {
            CompressSql::compress_delete_by_chapter($chapterId);
            // 相关表输出可能会与manga删除重复操作,使用deep变量区分是否需要做深度删除
            if ($deep) {
                // 删除相关历史记录
                HistorySql::delete_by_chapter($chapterId);

                // 删除相关书签
                BookMarkSql::delete_by_chapter($chapterId);

                // 删除相关收藏记录
                CollectSql::delete_by_chapter($chapterId);

                // 删除压缩转换记录
                CompressSql::compress_delete_by_chapter($chapterId);
            }
            return ['code' => 0, 'message' => '删除成功', 'request' => self::where('chapterId', $chapterId)->delete()];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
    /**
     * @description: 根据路径删除章节
     * @param {*} $pathId
     * @return {*}
     */
    public static function chapter_delete_by_path($pathId)
    {
        try {
            CompressSql::compress_delete_by_path($pathId);
            self::where('pathId', $pathId)->delete();
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
    /**
     * @description: 根据漫画id删除章节
     * @param {*} $chapterId
     * @return {*}
     */
    public static function chapter_delete_by_manga($mangaId)
    {
        try {
            CompressSql::compress_delete_by_manga($mangaId);
            self::where('mangaId', $mangaId)->delete();
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
    /**
     * @description: 根据媒体库id删除章节
     * @param {*} $mediaId
     * @return {*}
     */
    public static function chapter_delete_by_media($mediaId)
    {
        try {
            CompressSql::compress_delete_by_media($mediaId);
            self::where('mediaId', $mediaId)->delete();
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
    /**
     * @description: 转换排序参数
     * @param {*} $order
     * @return {*}
     */
    private static function get_order_text($order)
    {
        if(!$order) $order = 'name';

        $orderText = $order;
        if (array_search($order, ['id', 'idDesc']) !== false) {
            $orderText = 'chapterId';
        }
        if (array_search($order, ['name', 'nameDesc']) !== false) {
            $orderText = 'CAST(REGEXP_SUBSTR(chapterName, \'[0-9]+\') AS DECIMAL)';
        }
        if (array_search($order, ['time', 'timeDesc']) !== false) {
            $orderText = 'createTime';
        }

        $desc = preg_match('/Desc$/', $order) ? 'DESC' : 'ASC';

        return $orderText . ' ' . $desc;
    }

    /**
     * @description: 获取单个章节信息
     * @param {*} $chapterId
     * @return {*}
     */
    public static function chapter_info($chapterId){
        return self::where('chapterId', $chapterId)->first();
    }
}

class RequestList
{
    public $data;
    public $count;

    public function __construct($data, $count)
    {
        $this->data = $data;
        $this->count = $count;
    }
}
