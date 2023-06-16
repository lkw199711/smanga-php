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

    private $include;
    private $exclude;
    private $pathId;
    private $scanCount;
    private $scanIndex;
    private $mangaPath;
    private $mangaName;
    private $mangaCover;
    private $mangaType;
    private $defaultBrowse;
    private $direction;
    private $mediaId;
    private $mediaType;
    private $removeFirst;
    private $removeFirst;
    private $removeFirst;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pathInfo, $mangaItem)
    {
        //
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
                continue;
            }
            // 单本漫画
            $mangaData['chaptercount'] = 1;
            // 插入漫画
            $mangaInfo = MangaSql::add($mangaData);

            if ($mangaInfo['code'] == 1) {
                echo "error: 漫画 \"{$this->mangaName}\" 插入失败。{$mangaInfo['eMsg']}";
                continue;
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

            if (!$mangaInfo['request']) {
                // 漫画不存在则新增
                $mangaData['chaptercount'] = count($chapterList);
                // 插入漫画
                $mangaInfo = MangaSql::add($mangaData);
            }


            if ($mangaInfo['code'] == 1) {
                echo "error: 漫画 \"{$this->mangaName}\" 插入失败。{$mangaInfo['eMsg']}";
                continue;
            }

            // 插入章节
            foreach ($chapterList as $key => $value) {
                // 自定义排除目录
                if (!$this->include && $this->exclude) {
                    if (preg_match("/$this->exclude/", $value['path'])) {
                        continue;
                    }
                }

                // 更新扫描记录-进行中
                ScanSql::scan_update($this->pathId, [
                    'scanStatus' => 'scaning',
                    'targetPath' => $value['path'],
                ]);

                $chapterData = [
                    'mangaId' => $mangaInfo['request']->mangaId,
                    'mediaId' => $this->mediaId,
                    'pathId' => $this->pathId,
                    'chapterName' => $value['name'],
                    'chapterCover' => $value['poster'],
                    'chapterPath' => $value['path'],
                    'chapterType' => $value['type'],
                    'browseType' => $this->defaultBrowse
                ];

                ChapterSql::add($chapterData);
            }
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

                $posterName = preg_replace('/(.cbr|.cbz|.zip|.7z|.epub|.rar|.pdf)/i', '', $posterName);
            }

            array_push($list, array(
                'name' => $file,
                'poster' => self::get_poster($targetPath, $posterName),
                'path' => $targetPath,
                'type' => $type,
            ));
        }

        $dir->close();

        array_multisort(array_column($list, 'name'), SORT_NATURAL | SORT_FLAG_CASE, $list);

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
        $JPG = $name . '.jpg';

        if (is_file($png)) return $png;
        if (is_file($PNG)) return $PNG;
        if (is_file($jpg)) return $png;
        if (is_file($JPG)) return $JPG;

        return self::get_first_image($path);

        // if (count($list) > 0) {
        //     return $list[0];
        // } else {
        //     return '';
        // }
    }
}
