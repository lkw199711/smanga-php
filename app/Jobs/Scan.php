<?php
/*
 * @Author: error: error: git config user.name & please set dead value or install git && error: git config user.email & please set dead value or install git & please set dead value or install git
 * @Date: 2023-05-16 23:33:11
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 01:11:20
 * @FilePath: /php/laravel/app/Jobs/Scan.php
 */

namespace App\Jobs;

use App\Http\Controllers\Utils;
use App\Models\ChapterSql;
use App\Models\LogSql;
use App\Models\MangaSql;
use App\Models\PathSql;
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
            LogSql::add_default("路径 {$this->path} 正在扫描，放弃当前扫描任务 $params");
            return false;
        }

        // 开始扫描
        self::scan_start();

        $mangaList = [];
        $mangaListSql = [];
        // 是否扫描二级目录
        if ($this->directoryFormat == 1) {
            $mangaList = self::scan_second_path();
        } else {
            $mangaList = self::scan_path($this->path);
        }

        // 获取路径下所有漫画
        $mangaListSql = DB::table('manga')->where('pathId', $this->pathId)->get();

        // 不进行扫描了 只比对删除记录
        if (count($mangaList) < count($mangaListSql)) {

            foreach ($mangaListSql as $sqlval) {
                $hasManga = false;
                foreach ($mangaList as $val) {
                    if ($val->mangaPath === $sqlval->mangaPath) {
                        $hasManga = true;
                        break;
                    }
                }

                if (!$hasManga) {
                    MangaSql::manga_delete($sqlval->mangaId);
                }
            }

            self::scan_end();
        } elseif (count($mangaList) == 0) {
            LogSql::add_warning("路径 {$this->path} 没有检测到漫画，请确认漫画文件存在以及媒体库设置!");
            // 如果为空目录 则直接结束扫描
            self::scan_end();
        } else {
            // 正常扫描
            $dispatchSync = Utils::config_read('debug', 'dispatchSync');

            for ($i = 0, $length = count($mangaList); $i < $length; $i++) {
                if ($dispatchSync) {
                    ScanManga::dispatchSync($this->pathInfo, $mangaList[$i], $length, $i + 1);
                } else {
                    ScanManga::dispatch($this->pathInfo, $mangaList[$i], $length, $i + 1)->onQueue('scan');
                }
            }
        }
    }

    /**
     * @description: 扫描多层级目录
     * @return {*}
     */
    protected function scan_second_path()
    {
        $floderList = self::get_manga_list($this->path);
        $mangaList = [];
        foreach ($floderList as $key => $value) {
            $mangaList = array_merge($mangaList, self::scan_path($value->mangaPath, $this->pathId, $this->mediaId));
        }

        return $mangaList;
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

        return $mangaList;
    }
    /**
     * @description: 扫描目录获取漫画列表
     * @param {*} $path
     * @return {*}
     */
    private function get_manga_list($path)
    {
        $type = 'image';
        $list = array();
        $dir = scandir($path);
        $dir = array_diff($dir, ['.', '..']);

        foreach ($dir as $file) {
            $targetPath = $path . "/" . $file;
            $posterName = $targetPath;

            // 自定义排除目录
            if ($this->include) {
                if (!preg_match("/$this->include/", $targetPath)) {
                    continue;
                }
            } elseif ($this->exclude) {
                if (preg_match("/$this->exclude/", $targetPath)) {
                    continue;
                }
            } else {
                if (preg_match("/smanga-info/", $targetPath)) {
                    continue;
                }
            }

            // 是文件
            if (!is_dir($targetPath)) {
                if (preg_match('/(.cbr|.cbz|.zip|.epub)$/i', $file)) {
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

        return $list;
    }

    /**
     * @description: 结束扫描
     * @return {*}
     */
    private function scan_end()
    {
        // 更新扫描记录-结束
        ScanSql::scan_update($this->pathId, [
            'scanStatus' => 'finish',
        ]);

        // 更新最新扫描时间
        PathSql::path_update_scan_time($this->pathId, date('Y-m-d H:i:s'));
    }

    /**
     * @description: 扫描开始
     * @return {*}
     */
    private function scan_start()
    {
        // 插入扫描记录
        ScanSql::add([
            'scanStatus' => 'start',
            'path' => $this->path,
            'pathId' => $this->pathId,
        ]);
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
