<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-23 23:44:12
 * @FilePath: /php/laravel/app/Models/Media.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\Controllers\JobDispatch;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'media';
    protected $primaryKey = 'mediaId';
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
     * @description: 获取媒体库列表
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function get($page, $pageSize, $mediaLimit)
    {
        $paginate = self::whereNotIn('mediaId', $mediaLimit)->paginate($pageSize, ['*'], 'page', $page);
        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });
        return new SqlList($list, $count);
    }
    /**
     * @description: 无限制获取数据库列表
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function get_nolimit($page, $pageSize)
    {
        $paginate = self::paginate($pageSize, ['*'], 'page', $page);
        
        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });
        return new SqlList($list, $count);
    }
    /**
     * @description: 新增媒体库
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("媒体库 '{$data['mediaName']}' 插入失败。", $e->getMessage());
        }
    }
    /**
     * @description: 修改媒体库信息
     * @param {*} $mediaId
     * @param {*} $data
     * @return {*}
     */
    public static function media_update($mediaId, $data)
    {
        try {
            return self::where('mediaId', $mediaId)->update($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("媒体库 '{$mediaId}' 更新失败。", $e->getMessage());
        }
    }
    /**
     * @description: 删除媒体库
     * @param {*} $mediaId
     * @return {*}
     */
    public static function media_delete($mediaId)
    {
        try {
            return self::where('mediaId', $mediaId)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("媒体库 '{$mediaId}' 删除失败。", $e->getMessage());
        }
    }
}
