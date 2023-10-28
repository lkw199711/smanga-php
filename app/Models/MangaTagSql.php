<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-28 17:48:57
 * @FilePath: \lar-demo\app\Models\MangaTagSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MangaTagSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'mangaTag';
    protected $primaryKey = 'mangaTagId';
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
     * @description: 获取日志
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function get($userId, $mangaId, $page, $pageSize)
    {
        $model = self::join('tag', 'tag.tagId', 'mangaTag.tagId')->whereIn('userId', [$userId * 1, 0])->where('mangaId', $mangaId);
        $sql = $model->toSql();
        $paginate = $model->paginate($pageSize, ['*'], 'page', $page);
        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }

    public static function count_order($slice)
    {
        $model = self::join('tag', 'tag.tagId', 'mangaTag.tagId')
            ->select('*', DB::raw('count(*) as num'))
            ->groupBy('mangaTag.tagId')
            ->orderBy('num', 'desc')
            ->take($slice);

        return $model->get();
    }

    /**
     * @description: 以无分页模式获取漫画标签
     * @param {*} $userId
     * @param {*} $mangaId
     * @return {*}
     */
    public static function get_nopage($userId, $mangaId)
    {
        $model = self::join('tag', 'tag.tagId', 'mangaTag.tagId')
            ->whereIn('tag.userId', [$userId, 0])
            ->where('mangaId', $mangaId);

        // $toSql = $sql->toSql();

        $list = $model->get();
        return new SqlList($list, count($list));
    }
    /**
     * @description: 获取全部扫描记录
     * @return {*}
     */
    public static function get_all()
    {
        return self::select()->get();
    }
    /**
     * @description: 新增漫画关联标签
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("漫画关联标签新增失败.", $e->getMessage());
        }
    }

    /**
     * @description: 删除漫画标签
     * @param {*} $scanId
     * @return {*}
     */
    public static function manga_tag_delete($mangaTagId)
    {
        try {
            return self::destroy($mangaTagId);
        } catch (\Exception $e) {
            return ErrorHandling::handle("漫画关联标签删除失败.", $e->getMessage());
        }
    }

    /**
     * @description: 根据漫画id删除
     * @param {*} $chapterId
     * @return {*}
     */
    public static function delete_by_mangaId($mangaId)
    {
        try {
            return self::where('mangaId', $mangaId)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("漫画关联标签删除失败.", $e->getMessage());
        }
    }
    
    /**
     * @description: 获取漫画相应的
     * @param {*} $userrId
     * @param {*} $mangaId
     * @return {*}
     */
    public static function get_by_mangaId($userrId, $mangaId)
    {
        $list = self::join('tag', 'tag.tagId', 'mangaTag.tagId')
            ->where('userrId', $userrId)
            ->where('mangaId', $mangaId)
            ->get();

        return $list;
    }
}
