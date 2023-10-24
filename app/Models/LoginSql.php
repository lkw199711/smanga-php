<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 17:10:53
 * @FilePath: /php/laravel/app/Models/Login.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'login';
    protected $primaryKey = 'loginId';
    protected $guarded = [];
    public $timestamps = true;

    const CREATED_AT = 'loginTime';
    /**
     * 表中仅有createTime字段 重新setUpdatedAt方法置空操作 解决报错
     */
    public function setUpdatedAt($value)
    {
    }
    /**
     * @description: 新增登录记录
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("登录记录新增失败.", $e->getMessage());
        }
    }
}
