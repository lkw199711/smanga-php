<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 13:40:56
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-08-03 04:47:46
 * @FilePath: \lar-demo\app\Models\characterSql.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class characterSql extends Model
{
    use HasFactory;

    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'character';
    protected $primaryKey = 'characterId';
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
     * @description: 获取用户书签列表-分页
     * @param {*} $userId
     * @param {*} $page
     * @return {*}
     */
    public static function get($mangaId)
    {
        $list = self::where('mangaId', $mangaId)->get();
        return ['code' => 0, 'list' => $list, 'text' => '请求成功'];
    }
    /**
     * @description: 获取用户书签列表
     * @param {*} $userId
     * @return {*}
     */
    public static function get_nopage($userId)
    {
        $list = self::where('userId', $userId)->get();
        return ['code' => 0, 'list' => $list, 'text' => '请求成功'];
    }
    /**
     * @description: 获取全部书签
     * @param {*} $page
     * @return {*}
     */
    public static function all($page = 1, $pageSize = 10)
    {
        $list = self::paginate($pageSize, ['*'], 'page', $page);
        return ['code' => 0, 'list' => $list, 'text' => '请求成功'];
    }
    /**
     * @description: 新增书签
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
     * @description: 移除删除
     * @param {*} $bookmarkId
     * @return {*}
     */
    public static function remove($bookmarkId)
    {
        try {
            return ['code' => 0, 'message' => '删除成功', 'request' => self::destroy($bookmarkId)];
        } catch (\Exception $e) {
            return ['code' => 1, 'message' => '系统错误', 'eMsg' => $e->getMessage()];
        }
    }
}
