<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-08-17 21:14:36
 * @FilePath: \lar-demo\app\Models\MangaTagSql.php
 */

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public static function get($userid, $mangaId, $page, $pageSize)
    {
        $res = self::whereIn('userid', [$userid, 0])->where('mangaId', $mangaId)->paginate($pageSize, ['*'], 'page', $page);
        return ['code' => 0, 'request' => '获取标签成功', 'list' => $res];
    }

    /**
     * @description: 以无分页模式获取漫画标签
     * @param {*} $userid
     * @param {*} $mangaId
     * @return {*}
     */
    public static function get_nopage($userid, $mangaId)
    {
        $res = self::join('tag', 'tag.tagId', 'mangaTag.tagId')
            ->whereIn('tag.userid', [$userid, 0])
            ->where('mangaId', $mangaId)
            ->get();
        return ['code' => 0, 'request' => '获取标签成功', 'list' => $res];
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
     * @description: 新增日志
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return ['code' => 0, 'message' => '添加成功', 'request' => self::create($data)];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
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
            return ['code' => 0, 'message' => '删除成功', 'request' => self::destroy($mangaTagId)];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
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
            $request = self::where('mangaId', $mangaId)->delete();

            return ['code' => 0, 'message' => '删除成功', 'request' => $request];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
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
        $res = self::join('tag', 'tag.tagId', 'mangaTag.tagId')
            ->where('userrId', $userrId)
            ->where('mangaId', $mangaId)
            ->get();

        return $res;
    }
}
