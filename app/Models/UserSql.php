<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-14 16:59:00
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 18:37:40
 * @FilePath: /php/laravel/app/Models/UesrSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSql extends Model
{
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'user';
    protected $primaryKey = 'userId';
    protected $guarded = [];
    protected $hidden = [
        'passWord',
    ];
    public $timestamps = true;

    const CREATED_AT = 'registerTime';
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
     * @description: 用户列表
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
     * @description: 检查用户是否存在
     * @param {*} $userName
     * @return {*}
     */
    public static function get_info_by_name($userName)
    {
        return self::where('userName', $userName)->first();
    }
    /**
     * @description: 用户注册
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("用户新增失败.", $e->getMessage());
        }
    }
    /**
     * @description: 修改用户信息
     * @param {*} $userId
     * @param {*} $data
     * @return {*}
     */
    public static function user_update($userId, $data)
    {
        try {
            return self::where('userId', $userId)->update($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("用户修改失败.", $e->getMessage());
        }
    }
    public static function user_delete($userId)
    {
        try {
            return self::where('userId', $userId)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("用户删除失败.", $e->getMessage());
        }
    }
    /**
     * @description: 获取媒体库限制
     * @param {*} $userId
     * @return {*}
     */
    public static function get_media_limit($userId)
    {
        $text = self::where('userId', $userId)->first()->mediaLimit;
        return explode('/', $text);
    }
}
