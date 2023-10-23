<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 02:57:52
 * @FilePath: /php/laravel/app/Models/Version.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VersionSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'version';
    protected $primaryKey = 'versionId';
    protected $guarded = [];
    protected $hidden = [];
    public $timestamps = true;

    const CREATED_AT = 'updateTime';
    /**
     * 表中仅有createTime字段 重新setUpdatedAt方法置空操作 解决报错
     */
    public function setUpdatedAt($value)
    {
    }
    public static function list()
    {
        try {
            return self::get();
        } catch (\Exception $e) {
            return ErrorHandling::handle('获取版本列表失败.', $e->getMessage());
        }
    }
    /**
     * @description: 新增版本记录
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle('版本记录新增错误.', $e->getMessage());
        }
    }
}
