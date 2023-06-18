<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-16 23:33:11
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-06-17 14:59:04
 * @FilePath: /php/laravel/app/Jobs/Scan.php
 */

namespace App\Jobs;

use App\Http\Controllers\Utils;
use App\Models\ChapterSql;
use App\Models\LogSql;
use App\Models\MangaSql;
use App\Models\ScanSql;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class Scan implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $pathId;
    private $path;
    private $mediaId;
    // 浏览方式
    private $defaultBrowse;
    // 媒体类型
    private $mediaType;
    // 目录类型
    private $directoryFormat;
    // 阅读朝向
    private $direction;
    // 移除首页
    private $removeFirst;
    private $include;
    private $exclude;
    private $pathInfo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pathId)
    {
        // 获取需要扫描的目录id
        $this->pathId = $pathId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 设置脚本无限时间执行
        set_time_limit(0);
        // 获取路径信息
        $pathInfo = DB::table('path')
            ->join('media', 'media.mediaId', 'path.mediaId')
            ->where('pathId', $this->pathId)
            ->first();

        $this->path = $pathInfo->path;
        $this->mediaId = $pathInfo->mediaId;
        $this->defaultBrowse = $pathInfo->defaultBrowse;
        $this->mediaType = $pathInfo->mediaType;
        $this->directoryFormat = $pathInfo->directoryFormat;
        $this->direction = $pathInfo->direction;
        $this->removeFirst = $pathInfo->removeFirst;
        $this->include = $pathInfo->include;
        $this->exclude = $pathInfo->exclude;

        $this->pathInfo = $pathInfo;

        // 如果目录正在被扫描,则放弃此次扫描任务
        if (ScanSql::get_by_pathid($this->pathId)) {
            $params = "pathId->{$this->pathId}";
            LogSql::add_default("路径正在扫描，放弃当前扫描任务 $params");
            return false;
        }

        // 插入扫描记录
        ScanSql::add([
            'scanStatus' => 'start',
            'path' => $this->path,
            'pathId' => $this->pathId,
        ]);

        // 是否扫描二级目录
        if ($this->directoryFormat == 1) {
            self::scan_second_path();
        } else {
            self::scan_path($this->path);
        }
    }

    /**
     * @description: 扫描多层级目录
     * @return {*}
     */
    protected function scan_second_path()
    {
        $mangaList = self::get_manga_list($this->path);
        foreach ($mangaList as $key => $value) {
            self::scan_path($value['path'], $this->pathId, $this->mediaId);
        }
    }
    /**
     * @description: 扫描目录
     * @param {*} $path
     * @return {*}
     */
    protected function scan_path($path)
    {
        // 自定义目录排除(媒体库) 其实仅排除漫画就可以 这里为节省性能 根目录不符合直接不扫描
        if (!$this->include && $this->exclude) {
            if (preg_match("/$this->exclude/", $path)) {
                return;
            }
        }

        // mysql插入值空置处理 (插入空字符串会报错)
        $this->direction = $this->direction ? $this->direction : 1;
        $this->removeFirst = $this->removeFirst ? $this->removeFirst : 0;

        $mangaList = self::get_manga_list($path);

        for ($i = 0, $length = count($mangaList); $i < $length; $i++) {
            ScanManga::dispatch($this->pathInfo, $mangaList[$i], $length, $i + 1)->onQueue('scan');
            // ScanManga::dispatchSync($this->pathInfo, $mangaList[$i], $length, $i + 1);
        }
    }
    /**
     * @description: 扫描目录获取漫画列表
     * @param {*} $path
     * @return {*}
     */
    private function get_manga_list($path)
    {
        $list = array();
        $dir = dir($path);
        $type = 'image';

        while (($file = $dir->read()) !== false) {
            if ($file == '.' || $file == '..') continue;

            $targetPath = $path . "/" . $file;

            $posterName = $targetPath;
            // 是文件
            if (!is_dir($targetPath)) {
                if (preg_match('/(.cbr|.cbz|.zip|.epub)/i', $file)) {
                    $type = 'zip';
                } elseif (preg_match('/.7z/i', $file)) {
                    $type = '7z';
                } elseif (preg_match('/.rar/i', $file)) {
                    $type = 'rar';
                } elseif (preg_match('/.pdf/i', $file)) {
                    $type = 'pdf';
                } else {
                    continue;
                }

                $posterName = preg_replace('/(.cbr|.cbz|.zip|.7z|.rar|.pdf)/i', '', $posterName);
            };

            array_push($list, new MangaItem($file, $targetPath, $type));
        }

        $dir->close();

        return $list;
    }
}

class MangaItem
{

    public $mangaName;
    public $mangaPath;
    public $mangaType;

    public function __construct($mangaName, $mangaPath, $mangaType)
    {
        $this->mangaName = $mangaName;
        $this->mangaPath = $mangaPath;
        $this->mangaType = $mangaType;
    }
}
