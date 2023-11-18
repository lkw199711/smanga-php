<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-19 02:07:25
 * @FilePath: /php/laravel/app/Models/CompressSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobsSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'jobs';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $hidden = [];
    public $timestamps = true;

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
     * @description: 获取转换记录
     * @param {*} $mediaLimit
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
     * @description: 删除转换记录
     * @param {*} $compressId
     * @return {*}
     */
    public static function jobs_delete($jobId)
    {
        try {
            return self::destroy($jobId);
        } catch (\Exception $e) {
            return ErrorHandling::handle("任务删除失败.", $e->getMessage());
        }
    }

}
