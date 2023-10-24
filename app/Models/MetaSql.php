<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-05-13 15:49:55
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 17:53:19
 * @FilePath: \lar-demo\app\Models\MetaSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'meta';
    protected $primaryKey = 'metaId';
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
     * @description: 获取漫画元数据
     * @param {*} $mangaId
     * @return {*}
     */
    public static function get($mangaId)
    {
        return self::where('mangaId', $mangaId)->get();
    }

    /**
     * @description: 获取全部元数据
     * @return {*}
     */
    public static function get_all()
    {
        return self::select()->get();
    }

    /**
     * @description: 新增元数据
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("元数据新增失败.", $e->getMessage());
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
            return ErrorHandling::handle("元数据删除失败.", $e->getMessage());
        }
    }
}
