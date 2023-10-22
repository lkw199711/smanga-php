<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-22 15:37:18
 * @FilePath: /php/laravel/app/Models/CollectSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'collect';
    protected $primaryKey = 'collectId';
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
     * @description: 获取漫画收藏列表
     * @param {*} $userId
     * @param {*} $page
     * @return {*}
     */
    public static function get_manga($userId, $page = 1, $pageSize = 10, $order)
    {
        $orderText = self::get_manga_order_text($order);
        
        $sql = self::join('manga', 'collect.mangaId', 'manga.mangaId')
            ->where('userId', '=', $userId)
            ->where('collectType', 'manga')
            ->orderByRaw($orderText);

        // 分页原型
        $paginate = $sql->paginate($pageSize, ['*'], 'page', $page);

        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }
    /**
     * @description: 获取章节收藏
     * @param {*} $userId
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function get_chapter($userId, $page = 1, $pageSize = 10, $order)
    {
        $orderText = self::get_chapter_order_text($order);

        $sql = self::join('manga', 'collect.mangaId', 'manga.mangaId')
            ->join('chapter', 'collect.chapterId', 'chapter.chapterId')
            ->where('userId', $userId)
            ->where('collectType', 'chapter')
            ->orderByRaw($orderText);

        $paginate = $sql->paginate($pageSize, ['*'], 'page', $page);

        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }
    /**
     * @description: 获取全部收藏
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
     * @description: 新增收藏
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            ErrorHandling::handle('新增收藏错误', $e->getMessage());
        }
    }
    /**
     * @description: 移除收藏
     * @param {*} $collectId
     * @return {*}
     */
    public static function remove($collectId)
    {
        try {
            return self::destroy($collectId);
        } catch (\Exception $e) {
            ErrorHandling::handle('移除收藏错误', $e->getMessage());
        }
    }
    /**
     * @description: 删除漫画收藏
     * @param {*} $userId
     * @param {*} $mangaId
     * @return {*}
     */
    public static function remove_manga($userId, $mangaId)
    {
        try {
            $res = self::where('userId', $userId)->where('collectType', 'manga')->where('mangaId', $mangaId)->delete();
            return $res;
        } catch (\Exception $e) {
            ErrorHandling::handle("移除漫画收藏错误,mangaId=>$mangaId", $e->getMessage());
        }
    }
    /**
     * @description: 删除章节收藏
     * @param {*} $userId
     * @param {*} $chapterId
     * @return {*}
     */
    public static function remove_chapter($userId, $chapterId)
    {
        try {
            $res = self::where('userId', $userId)->where('collectType', 'chapter')->where('chapterId', $chapterId)->delete();
            return $res;
        } catch (\Exception $e) {
            ErrorHandling::handle("移除章节收藏错误,chapterId=>$chapterId", $e->getMessage());
        }
    }
    /**
     * @description: 验证漫画是否收藏
     * @param {*} $userId
     * @param {*} $mangaId
     * @return {*}
     */
    public static function is_manga_collected($userId, $mangaId)
    {
        $res = self::where('userId', $userId)->where('collectType', 'manga')->where('mangaId', $mangaId)->first();
        return $res;
    }
    /**
     * @description: 验证章节是否收藏
     * @param {*} $userId
     * @param {*} $chapterId
     * @return {*}
     */
    public static function is_chapter_collected($userId, $chapterId)
    {
        $res = self::where('userId', $userId)->where('collectType', 'chapter')->where('chapterId', $chapterId)->first();
        return $res;
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
            ErrorHandling::handle("移除漫画收藏错误,mangaId=>$mangaId", $e->getMessage());
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
            ErrorHandling::handle("移除章节收藏错误,chapterId=>$chapterId", $e->getMessage());
        }
    }

    /**
     * @description: 转换排序参数
     * @param {*} $order
     * @return {*}
     */
    private static function get_manga_order_text($order)
    {
        if (!$order) $order = 'name';

        $orderText = $order;
        if (array_search($order, ['id', 'idDesc']) !== false) {
            $orderText = 'manga.mangaId';
        }
        if (array_search($order, ['name', 'nameDesc']) !== false) {
            $orderText = 'manga.mangaName';
        }
        if (array_search($order, ['time', 'timeDesc']) !== false) {
            $orderText = 'collect.createTime';
        }

        $desc = preg_match('/Desc$/', $order) ? 'DESC' : 'ASC';

        return $orderText . ' ' . $desc;
    }
    /**
     * @description: 转换排序参数
     * @param {*} $order
     * @return {*}
     */
    private static function get_chapter_order_text($order)
    {
        if (!$order) $order = 'name';

        $orderText = $order;
        if (array_search($order, ['id', 'idDesc']) !== false) {
            $orderText = 'chapter.chapterId';
        }
        if (array_search($order, ['name', 'nameDesc']) !== false) {
            $orderText = 'CAST(REGEXP_SUBSTR(chapter.chapterName, \'[0-9]+\') AS DECIMAL)';
        }
        if (array_search($order, ['time', 'timeDesc']) !== false) {
            $orderText = 'collect.createTime';
        }

        $desc = preg_match('/Desc$/', $order) ? 'DESC' : 'ASC';

        return $orderText . ' ' . $desc;
    }
}
