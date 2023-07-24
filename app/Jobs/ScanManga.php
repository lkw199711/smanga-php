<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Utils;
use App\Models\ChapterSql;
use App\Models\LogSql;
use App\Models\ScanSql;
use App\Models\MangaSql;

class ScanManga implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // path属性
    private $include;
    private $exclude;
    private $pathId;
    private $mediaType;
    private $scanCount;
    private $scanIndex;
    private $mangaPath;
    // manga属性
    private $mangaName;
    private $mangaCover;
    private $mangaType;
    private $defaultBrowse;
    private $direction;
    private $removeFirst;
    private $mediaId;

    // private $removeFirst;
    // private $removeFirst;
    // private $removeFirst;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pathInfo, $mangaItem, $scanCount, $scanIndex)
    {
        //
        $this->pathId = $pathInfo->pathId;
        $this->mediaId = $pathInfo->mediaId;
        $this->defaultBrowse = $pathInfo->defaultBrowse;
        $this->mediaType = $pathInfo->mediaType;
        $this->direction = $pathInfo->direction;
        $this->removeFirst = $pathInfo->removeFirst;
        $this->include = $pathInfo->include;
        $this->exclude = $pathInfo->exclude;

        $this->mangaName = $mangaItem->mangaName;
        $this->mangaPath = $mangaItem->mangaPath;
        $this->mangaType = $mangaItem->mangaType;

        $this->scanCount = $scanCount;
        $this->scanIndex = $scanIndex;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        // 自定义排除目录
        if ($this->include) {
            if (!preg_match("/$this->include/", $this->mangaPath)) {
                return false;
            }
        } elseif ($this->exclude) {
            if (preg_match("/$this->exclude/", $this->mangaPath)) {
                return false;
            }
        }

        // 更新扫描记录-进行中
        ScanSql::scan_update($this->pathId, [
            'scanStatus' => 'scaning',
            'targetPath' => $this->mangaPath,
            'scanCount' => $this->scanCount,
            'scanIndex' => $this->scanIndex
        ]);

        // 获取封面
        $this->mangaCover = self::get_poster($this->mangaPath, $this->mangaPath);

        $mangaData = [
            'mediaId' => $this->mediaId,
            'pathId' => $this->pathId,
            'mangaName' => $this->mangaName,
            'mangaCover' => $this->mangaCover,
            'mangaPath' => $this->mangaPath,
            'browseType' => $this->defaultBrowse,
            'direction' => $this->direction,
            'removeFirst' => $this->removeFirst
        ];

        // 检查库中是否存在此漫画
        $mangaInfo = ['code' => 0, 'request' => DB::table('manga')->where('mangaPath', $this->mangaPath)->where('mediaId', $this->mediaId)->first()];

        if ($this->mediaType == 1) {

            if ($mangaInfo['request']) {
                // 漫画已存在 且为单本 跳过此漫画
                return false;
            }
            // 单本漫画
            $mangaData['chaptercount'] = 1;
            // 插入漫画
            $mangaInfo = MangaSql::add($mangaData);

            if ($mangaInfo['code'] == 1) {
                echo "error: 漫画 \"{$this->mangaName}\" 插入失败。{$mangaInfo['eMsg']}";
                return false;
            }

            $chapterData = [
                'mangaId' => $mangaInfo['request']->mangaId,
                'mediaId' => $this->mediaId,
                'pathId' => $this->pathId,
                'chapterName' => $this->mangaName,
                'chapterCover' => $this->mangaCover,
                'chapterPath' => $this->mangaPath,
                'chapterType' => $this->mangaType,
                'browseType' => $this->defaultBrowse
            ];

            ChapterSql::add($chapterData);
        } else {
            // 普通结构

            $chapterList = self::get_chapter_list($this->mangaPath);
            $chapterListSql = [];

            if (!$mangaInfo['request']) {
                // 漫画不存在则新增
                $mangaData['chaptercount'] = count($chapterList);
                // 插入漫画
                $mangaInfo = MangaSql::add($mangaData);
            } else {
                // 漫画原本存在,获取所有漫画章节进行增减判断
                $mangaId = $mangaInfo['request']->mangaId;
                $res = ChapterSql::get_nopage($mangaId, 'id');
                $chapterListSql = $res['list']->data;
            }

            // 获取漫画是吧(数据库错误)
            if ($mangaInfo['code'] == 1) {
                echo "error: 漫画 \"{$this->mangaName}\" 插入失败。{$mangaInfo['eMsg']}";
                // 记录错误日志
                LogSql::add([
                    'logType' => 'error',
                    'logLevel' => 3,
                    'logContent' => "error: 漫画 \"{$this->mangaName}\" 插入失败。{$mangaInfo['eMsg']}"
                ]);
                return false;
            }

            // 实际目录扫描多于数据库章节 (说明新增了章节)
            if (count($chapterList) > count($chapterListSql)) {
                foreach ($chapterList as $val) {

                    // 自定义排除目录
                    if (!$this->include && $this->exclude) {
                        if (preg_match("/$this->exclude/", $val->chapterPath)) {
                            continue;
                        }
                    }

                    // 更新扫描记录-进行中
                    ScanSql::scan_update($this->pathId, [
                        'scanStatus' => 'scaning',
                        'targetPath' => $val->chapterPath,
                    ]);

                    $hasChapter = false;
                    foreach ($chapterListSql as $sqlval) {
                        if ($val->chapterPath === $sqlval->chapterPath) {
                            $hasChapter = true;
                            break;
                        }
                    }

                    // 没有章节 进行新增
                    if (!$hasChapter) {
                        $chapterData = [
                            'mangaId' => $mangaInfo['request']->mangaId,
                            'mediaId' => $this->mediaId,
                            'pathId' => $this->pathId,
                            'chapterName' => $val->chapterName,
                            'chapterCover' => $val->chapterCover,
                            'chapterPath' => $val->chapterPath,
                            'chapterType' => $val->chapterType,
                            'browseType' => $this->defaultBrowse
                        ];

                        ChapterSql::add($chapterData);
                    }
                }
            }

            // 实际目录扫描少于数据库章节 (说明删除了章节)
            if (count($chapterList) < count($chapterListSql)) {
                foreach ($chapterListSql as $sqlval) {
                    // 更新扫描记录-进行中
                    ScanSql::scan_update($this->pathId, [
                        'scanStatus' => 'scaning',
                        'targetPath' => $sqlval->chapterPath,
                    ]);

                    $hasChapter = false;
                    foreach ($chapterList as $val) {
                        if ($val->chapterPath === $sqlval->chapterPath) {
                            $hasChapter = true;
                            break;
                        }
                    }

                    // 目录实际没有此章节 在数据库中删除
                    if (!$hasChapter) {
                        ChapterSql::chapter_delete($sqlval->chapterId);
                    }
                }
            }
        }

        // 更新扫描记录-扫描结束
        if ($this->scanIndex >= $this->scanCount) {
            ScanSql::scan_update($this->pathId, [
                'scanStatus' => 'finish'
            ]);

            // 扫描结束,删除扫描记录
            ScanSql::scan_delete_by_pathid($this->pathId);

            //Utils::socket_send_array($this->userId, 0, '解压完成', $chapterInfo->mangaName . ':\n\r' . $chapterInfo->chapterName);
        }
    }
    /**
     * @description: 扫描目录获取章节列表
     * @param {*} $path
     * @return {*}
     */
    private function get_chapter_list($path)
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

                $posterName = preg_replace('/(.cbr|.cbz|.zip|.7z|.epub|.rar|.pdf)$/i', '', $posterName);
            }

            array_push($list, new ChapterItem($file, $targetPath, self::get_poster($targetPath, $posterName), $type));
        }

        $dir->close();

        return $list;
    }

    /**
     * @description: 递归扫描目录
     * @param {*} $path
     * @return {*}
     */
    private function get_file_list($path)
    {
        $list = array();

        if (!is_dir($path)) {
            return $list;
        }

        $dir = dir($path);

        while (($file = $dir->read()) !== false) {
            if ($file == '.' || $file == '..') continue;

            $route = $path . "/" . $file;

            // 添加图片
            if (Utils::is_img($route)) {
                array_push($list, $route);
            }
            // 遍历所有路径
            if (is_dir($route)) {
                $list = array_merge($list, self::get_file_list($route));
            }
        }

        $dir->close();

        sort($list, SORT_NATURAL | SORT_FLAG_CASE);

        return $list;
    }

    /**
     * @description: 递归遍历目录 返回第一张图片
     * @param {*} $path
     * @return {*}
     */
    private function get_first_image($path)
    {
        if (!is_dir($path)) {
            return '';
        }

        $dir = dir($path);

        while (($file = $dir->read()) !== false) {
            if ($file == '.' || $file == '..') continue;

            $route = $path . "/" . $file;

            // 添加图片
            if (Utils::is_img($route)) {
                return $route;
            }
            // 遍历所有路径
            if (is_dir($route)) {
                return self::get_first_image($route);
            }
        }

        $dir->close();

        return '';
    }

    /**
     * @description: 获取目录封面
     * @param {*} $path
     * @param {*} $name
     * @return {*}
     */
    private function get_poster($path, $name)
    {
        $png = $name . '.png';
        $PNG = $name . '.PNG';
        $jpg = $name . '.jpg';
        $JPG = $name . '.JPG';

        if (is_file($png)) return $png;
        if (is_file($PNG)) return $PNG;
        if (is_file($jpg)) return $jpg;
        if (is_file($JPG)) return $JPG;

        return self::get_first_image($path);

        // if (count($list) > 0) {
        //     return $list[0];
        // } else {
        //     return '';
        // }
    }
}

class ChapterItem
{
    public $chapterName;
    public $chapterPath;
    public $chapterType;
    public $chapterCover;

    public function __construct($chapterName, $chapterPath, $chapterCover, $chapterType)
    {
        $this->chapterName = $chapterName;
        $this->chapterPath = $chapterPath;
        $this->chapterCover = $chapterCover;
        $this->chapterType = $chapterType;
    }
}
