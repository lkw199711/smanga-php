<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-25 01:46:54
 * @FilePath: \lar-demo\app\Models\TagSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'tag';
    protected $primaryKey = 'tagId';
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
    public static function get($userId, $page, $pageSize)
    {
        $paginate = self::where('userId', $userId)->paginate($pageSize, ['*'], 'page', $page);
        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }
    /**
     * @description: 获取当前用户的全部标签
     * @param {*} $userId
     * @return {*}
     */
    public static function get_no_page($userId)
    {
        $list = self::whereIn('tag.userId', [$userId, 0])->get();
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
     * @description: 新增日志
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            ErrorHandling::handle('标签插入错误', $e->getMessage());
        }
    }
    /**
     * @description: 更新标签记录
     * @param {*} $pathId
     * @param {*} $data
     * @return {*}
     */
    public static function tag_update($tagId, $data)
    {
        try {
            return self::where('tagId', $tagId)->update($data);
        } catch (\Exception $e) {
            ErrorHandling::handle('标签修改错误', $e->getMessage());
        }
    }
    /**
     * @description: 删除标签记录
     * @param {*} $scanId
     * @return {*}
     */
    public static function tag_delete($tagId)
    {
        try {
            return self::destroy($tagId);
        } catch (\Exception $e) {
            ErrorHandling::handle('标签删除错误', $e->getMessage());
        }
    }

    /**
     * @description: 是否有某个标签
     * @param {*} $tagName
     * @return {*}
     */
    public static function has_tag($tagName)
    {
        return self::where('userId', 0)->where('tagName', $tagName)->first();
    }
}
