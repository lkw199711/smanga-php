<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-07-28 08:36:00
 * @FilePath: \lar-demo\app\Models\TagSql.php
 */

namespace App\Models;

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
        $res = self::where('userId', $userId)->paginate($pageSize, ['*'], 'page', $page);
        return ['code' => 0, 'request' => '获取标签成功', 'list' => $res];
    }
    /**
     * @description: 获取当前用户的全部标签
     * @param {*} $userId
     * @return {*}
     */
    public static function get_no_page($userId)
    {
        $res = self::where('userId', $userId)->get();
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
     * @description: 更新扫描记录
     * @param {*} $pathId
     * @param {*} $data
     * @return {*}
     */
    public static function tag_update($tagId, $data)
    {
        try {
            return ['code' => 0, 'message' => '修改成功', 'request' => self::where('tagId', $tagId)->update($data)];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
    /**
     * @description: 删除扫描记录
     * @param {*} $scanId
     * @return {*}
     */
    public static function tag_delete($tagId)
    {
        try {
            return ['code' => 0, 'message' => '删除成功', 'request' => self::destroy($tagId)];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
}
