<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 18:08:09
 * @FilePath: \lar-demo\app\Models\ScanSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'scan';
    protected $primaryKey = 'scanId';
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
    public static function get($page, $pageSize)
    {
        $paginate = self::paginate($pageSize, ['*'], 'page', $page);
        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
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
     * @description: 根据pathid获取
     * @param {*} $pathId
     * @return {*}
     */
    public static function get_by_pathid($pathId)
    {
        return self::where('pathId', $pathId)->first();
    }
    /**
     * @description: 新增扫描记录
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("扫描记录新增失败.", $e->getMessage());
        }
    }
    /**
     * @description: 更新扫描记录
     * @param {*} $pathId
     * @param {*} $data
     * @return {*}
     */
    public static function scan_update($pathId, $data)
    {
        try {
            return self::where('pathId', $pathId)->update($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("扫描记录更新失败.", $e->getMessage());
        }
    }
    /**
     * @description: 删除扫描记录
     * @param {*} $scanId
     * @return {*}
     */
    public static function scan_delete($scanId)
    {
        try {
            return self::destroy($scanId);
        } catch (\Exception $e) {
            return ErrorHandling::handle("扫描记录删除失败.", $e->getMessage());
        }
    }
    /**
     * @description: 根据pathid删除扫描记录
     * @param {*} $pathId
     * @return {*}
     */
    public static function scan_delete_by_pathid($pathId)
    {
        try {
            return self::where('pathId', $pathId)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("扫描记录删除失败.", $e->getMessage());
        }
    }
    /**
     * @description: 根据createTime清除日志
     * @param {*} $createTime
     * @return {*}
     */
    public static function delete_by_create($createTime)
    {
        try {
            self::where('createTime', '>', $createTime)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("扫描记录删除失败.", $e->getMessage());
        }
    }
}
