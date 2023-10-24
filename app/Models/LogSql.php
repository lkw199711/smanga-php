<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 17:18:17
 * @FilePath: \lar-demo\app\Models\Log.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'log';
    protected $primaryKey = 'logId';
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
     * @description: 新增日志
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("日志新增失败.", $e->getMessage());
        }
    }
    /**
     * @description: 写入默认日志（流程 0级别
     * @param {*} $logContent
     * @return {*}
     */
    public static function add_default($logContent)
    {
        try {
            return self::create(['logContent' => $logContent]);
        } catch (\Exception $e) {
            return ErrorHandling::handle("日志新增失败.", $e->getMessage());
        }
    }

    /**
     * @description: 写入默认日志（流程 0级别
     * @param {*} $logContent
     * @return {*}
     */
    public static function add_warning($logContent)
    {
        try {
            return self::create(['logType' => 'warning', 'logLevel' => 1, 'logContent' => $logContent]);
        } catch (\Exception $e) {
            return ErrorHandling::handle("日志新增失败.", $e->getMessage());
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
            return ErrorHandling::handle("日志删除失败.", $e->getMessage());
        }
    }
}
