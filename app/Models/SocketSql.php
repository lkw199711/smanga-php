<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-21 20:54:48
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 18:24:36
 * @FilePath: /php/laravel/app/Models/SocketSql.php
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocketSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'socket';
    protected $primaryKey = 'socketId';
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
     * @description: 新增长连接
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("长连接新增失败.", $e->getMessage());
        }
    }
    public static function socket_update($fd, $data)
    {
        try {
            return self::updateOrCreate(['fd' => $fd], $data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("长连接更新失败.", $e->getMessage());
        }
    }

    /**
     * @description: 移除长连接
     * @param {*} $socketId
     * @return {*}
     */
    public static function socket_delete($fd)
    {
        try {
            return self::where('fd', $fd)->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("长连接移除失败.", $e->getMessage());
        }
    }
    /**
     * @description: 根据用户id获取链接
     * @param {*} $userId
     * @return {*}
     */
    public static function get_fd_by_user($userId)
    {
        try {
            $list = self::where('userId', $userId)->get();

            // 取出fd
            $fds = [];

            foreach ($list as $value) {
                array_push($fds, $value->fd);
            }

            return $fds;
        } catch (\Exception $e) {
            return ErrorHandling::handle("长连接获取失败.", $e->getMessage());
        }
    }
    public static function clear()
    {
        try {
            return self::query()->delete();
        } catch (\Exception $e) {
            return ErrorHandling::handle("长连接清空失败.", $e->getMessage());
        }
    }
}
