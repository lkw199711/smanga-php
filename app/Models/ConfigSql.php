<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-23 23:09:10
 * @FilePath: /php/laravel/app/Models/Config.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\InterfacesResponse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfigSql

extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'config';
    protected $primaryKey = 'configId';
    protected $guarded = [];
    public $timestamps = true;

    const CREATED_AT = 'createTime';
    const UPDATED_AT = 'updateTime';

    /**
     * @description: 获取用户配置信息
     * @param {*} $userId
     * @return {*}
     */
    public static function get($userId)
    {
        return self::where('userId', $userId)->first();
    }
    /**
     * @description: 设置用户配置
     * @param {*} $data
     * @return {*}
     */
    public static function set($userId, $data)
    {
        try {
            return self::updateOrCreate(['userId' => $userId], $data);
        } catch (\Exception $e) {
            return ErrorHandling::handle('用户配置设置失败.', $e->getMessage());
        }
    }
}
