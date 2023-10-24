<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 13:40:56
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 16:46:08
 * @FilePath: \lar-demo\app\Models\CharacterSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharacterSql extends Model
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
     * @description: 获取角色列表-分页
     * @param {*} $userId
     * @param {*} $page
     * @return {*}
     */
    public static function get($mangaId)
    {
        $list = self::where('mangaId', $mangaId)->get();
        $sqlList = new SqlList($list, count($list));
        return $sqlList;
    }
    /**
     * @description: 获取角色列表
     * @param {*} $userId
     * @return {*}
     */
    public static function get_nopage($userId)
    {
        $list = self::where('userId', $userId)->get();
        return new SqlList($list, count($list));
    }
    /**
     * @description: 获取全部角色
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
        return new SqlList($list, count($count));
    }
    /**
     * @description: 角色书签
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle('角色新增失败.', $e->getMessage());
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
            self::destroy($bookmarkId);
        } catch (\Exception $e) {
            return ErrorHandling::handle('角色删除失败.', $e->getMessage());
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
            return ErrorHandling::handle('角色删除失败.', $e->getMessage());
        }
    }
}
