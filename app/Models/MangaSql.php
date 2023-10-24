<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-13 20:17:40
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-25 01:57:23
 * @FilePath: /php/laravel/app/Models/MangaSql.php
 * @Description: 这是默认设置,请设置`customMade`, 打开koroFileHeader查看配置 进行设置: https://github.com/OBKoro1/koro1FileHeader/wiki/%E9%85%8D%E7%BD%AE
 */

namespace App\Models;

use App\Http\Controllers\ErrorHandling;
use App\Http\Controllers\History;
use App\Http\PublicClass\SqlList;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangaSql extends Model
{
    use HasFactory;
    /**
     * 与模型关联的表名
     *
     * @var string
     */
    protected $table = 'manga';
    protected $primaryKey = 'mangaId';
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
     * @description: 获取漫画列表
     * @param {*} $page
     * @param {*} $pageSize
     * @param {*} $mediaLimit
     * @return {*}
     */
    public static function get($page, $pageSize, $mediaId, $mediaLimit, $userId, $order)
    {
        $orderText = self::get_order_text($order);

        $sql = self::where('mediaId', $mediaId)->whereNotIn('mediaId', $mediaLimit)->orderByRaw($orderText);

        $res = $sql->paginate($pageSize, ['*'], 'page', $page);

        $count = $res->total();

        $list = $res->getCollection()->transform(function ($row) use ($userId) {
            $tagArr = MangaTagSql::get_nopage($userId, $row->mangaId);

            $characterRes = CharacterSql::get($row->mangaId);
            $characters = $characterRes->list;
            $metaRes = MetaSql::get($row->mangaId);
            $metas = $metaRes;

            $row->tags = $tagArr->list;
            $row->characters = $characters;
            $row->metas = $metas;
            return $row;
        });

        return new SqlList($list, $count);
    }

    /**
     * @description: 获取漫画信息与元数据
     * @param {*} $mangaId
     * @return {*}
     */
    public static function get_manga_info($mangaId, $userId)
    {
        $info = self::where('mangaId', $mangaId)->first();
        $meta = MetaSql::get($mangaId);
        $character = CharacterSql::get($mangaId)->list;
        $tags = MangaTagSql::get_nopage($userId, $mangaId)->list;

        return [
            'info' => $info,
            'tags' => $tags,
            'meta' => $meta,
            'character' => $character
        ];
    }

    /**
     * @description: 获取全部媒体库的漫画
     * @param {*} $page
     * @param {*} $pageSize
     * @param {*} $mediaLimit
     * @return {*}
     */
    public static function get_nomedia($page, $pageSize, $mediaLimit)
    {
        $paginate = self::whereNotIn('mediaId', $mediaLimit)->paginate($pageSize, ['*'], 'page', $page);

        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }
    /**
     * @description: 无限制获取漫画列表
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function get_nolimit($page, $pageSize, $mediaId)
    {
        $paginate = self::where('mediaId', $mediaId)->paginate($pageSize, ['*'], 'page', $page);
        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });
        return new SqlList($list, $count);
    }
    /**
     * @description: 新增漫画
     * @param {*} $data
     * @return {*}
     */
    public static function add($data)
    {
        try {
            return self::create($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("漫画 '{$data['mangaName']}' 插入失败。", $e->getMessage());
        }
    }
    /**
     * @description: 搜索漫画
     * @param {*} $keyWord
     * @param {*} $mediaId
     * @param {*} $mediaLimit
     * @param {*} $page
     * @param {*} $pageSize
     * @return {*}
     */
    public static function manga_search($keyWord, $mediaLimit, $order, $page, $pageSize, $userId = 0)
    {
        $orderText = self::get_order_text($order);
        $sql = self::whereNotIn('mediaId', $mediaLimit)->where('mangaName', 'like', "%{$keyWord}%")->orderByRaw($orderText);

        $paginate = $sql->paginate($pageSize, ['*'], 'page', $page);

        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) use ($userId) {
            $tagArr = MangaTagSql::get_nopage($userId, $row->mangaId);

            $characterRes = CharacterSql::get($row->mangaId);
            $characters = $characterRes->list;
            $metaRes = MetaSql::get($row->mangaId);
            $metas = $metaRes;

            $row->tags = $tagArr->list;
            $row->characters = $characters;
            $row->metas = $metas;
            return $row;
        });

        return new SqlList($list, $count);
    }
    /**
     * @description: 修改漫画信息
     * @param {*} $mangaId
     * @param {*} $data
     * @return {*}
     */
    public static function manga_update($mangaId, $data)
    {
        try {
            return self::where('mangaId', $mangaId)->update($data);
        } catch (\Exception $e) {
            return ErrorHandling::handle("漫画 '{$mangaId}' 修改失败。", $e->getMessage());
        }
    }
    /**
     * @description: 移除漫画
     * @param {*} $mangaId
     * @return {*}
     */
    public static function manga_delete($mangaId)
    {
        try {
            // 删除相关章节
            ChapterSql::chapter_delete_by_manga($mangaId, false);

            // 删除相关标签关联
            MangaTagSql::delete_by_mangaId($mangaId);

            // 删除角色元数据
            CharacterSql::delete_by_mangaId($mangaId);

            // 删除其他元数据
            MetaSql::delete_by_mangaId($mangaId);

            // 删除相关历史记录
            HistorySql::delete_by_mangaid($mangaId);

            // 删除最后阅读标记
            LastReadSql::delete_by_mangaid($mangaId);

            // 删除相关书签
            BookMarkSql::delete_by_mangaid($mangaId);

            // 删除相关收藏记录
            CollectSql::delete_by_mangaid($mangaId);

            // 删除压缩转换记录
            CompressSql::compress_delete_by_manga($mangaId);

            return self::where('mangaId', $mangaId)->delete();;
        } catch (\Exception $e) {
            return ErrorHandling::handle("漫画 '{$mangaId}' 删除失败。", $e->getMessage());
        }
    }
    /**
     * @description: 根据路径删除漫画
     * @param {*} $pathId
     * @return {*}
     */
    public static function manga_delete_by_path($pathId)
    {
        try {
            $mangas = self::where('pathId', $pathId)->get();
            foreach ($mangas as $val) {
                self::manga_delete($val->mangaId);
            }
        } catch (\Exception $e) {
            return ErrorHandling::handle("路径 '{$pathId}' 删除失败。", $e->getMessage());
        }
    }
    /**
     * @description: 根据媒体库id删除漫画
     * @param {*} $mediaId
     * @return {*}
     */
    public static function manga_delete_by_media($mediaId)
    {
        try {
            $mangas = self::where('mediaId', $mediaId)->get();
            foreach ($mangas as $val) {
                self::manga_delete($val->mangaId);
            }
        } catch (\Exception $e) {
            return ErrorHandling::handle("媒体库 '{$mediaId}' 删除失败。", $e->getMessage());
        }
    }
    /**
     * @description: 通过标签获取漫画
     * @return {*}
     */
    public static function get_by_tags($tagIdArr, $page, $pageSize, $order)
    {
        $orderText = self::get_order_text($order);

        $model = self::join('mangaTag', 'mangaTag.mangaId', 'manga.mangaId')
            ->whereIn('tagId', $tagIdArr)->orderByRaw($orderText)->groupBy('mangaTag.mangaId');

        $paginate = $model->paginate($pageSize, ['*'], 'page', $page);

        $count = $paginate->total();
        $list = $paginate->getCollection()->transform(function ($row) {
            return $row;
        });

        return new SqlList($list, $count);
    }
    /**
     * @description: 转换排序参数
     * @param {*} $order
     * @return {*}
     */
    private static function get_order_text($order)
    {
        if (!$order) $order = 'name';

        $orderText = $order;
        if (array_search($order, ['id', 'idDesc']) !== false) {
            $orderText = 'manga.mangaId';
        }
        if (array_search($order, ['name', 'nameDesc']) !== false) {
            $orderText = 'manga.mangaName';
        }
        if (array_search($order, ['time', 'timeDesc']) !== false) {
            $orderText = 'manga.createTime';
        }

        $desc = preg_match('/Desc$/', $order) ? 'DESC' : 'ASC';

        return $orderText . ' ' . $desc;
    }

    /**
     * @description: 更新update时间戳
     * @param {*} $mangaId
     * @return {*}
     */
    public static function manga_touch($mangaId)
    {
        self::find($mangaId)->touch();
    }
}
