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
use App\Models\characterSql;
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

        $posterName = preg_replace('/(.cbr|.cbz|.zip|.7z|.epub|.rar|.pdf)$/i', '', $this->mangaPath);
        // 获取封面
        $this->mangaCover = self::get_poster($this->mangaPath, $posterName);

        $mangaInsert = [
            'mediaId' => $this->mediaId,
            'pathId' => $this->pathId,
            'mangaName' => $this->mangaName,
            'mangaCover' => $this->mangaCover,
            'mangaPath' => $this->mangaPath,
            'browseType' => $this->defaultBrowse,
            'direction' => $this->direction,
            'removeFirst' => $this->removeFirst
        ];

        // 扫描元数据
        $mangaMetaPath = self::get_meta_path($this->mangaPath);
        if (is_dir($mangaMetaPath)) {
            $mangaInsert = array_merge($mangaInsert, self::get_manga_meta($mangaMetaPath));
        }

        // 检查库中是否存在此漫画
        $mangaInfo = ['code' => 0, 'request' => DB::table('manga')->where('mangaPath', $this->mangaPath)->where('mediaId', $this->mediaId)->first()];

        if ($this->mediaType == 1) {

            if ($mangaInfo['request']) {
                // 漫画已存在 且为单本 跳过此漫画
                return false;
            }
            // 单本漫画
            $mangaInsert['chaptercount'] = 1;
            // 插入漫画
            $mangaInfo = MangaSql::add($mangaInsert);

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
                $mangaInsert['chaptercount'] = count($chapterList);
                // 插入漫画
                $mangaInfo = MangaSql::add($mangaInsert);
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

        // 元数据其他操作
        if (is_dir($mangaMetaPath)) {
            $tags = self::get_manga_tags($mangaMetaPath);
            $character = self::get_manga_character($mangaMetaPath);

            foreach($character as $val){
                
                $characterPicture = $mangaMetaPath . '/' . $val->name . '.jpg';
                if (!is_file($characterPicture)) $characterPicture='';

                characterSql::add([
                    'characterName'=> $val->name,
                    'description'=> $val->description,
                    'characterPicture'=> $characterPicture,
                    'mangaId'=>$mangaInfo['request']->mangaId,
                ]);
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

    /**
     * @description: 获取smanga-info的路径
     * @param {*} $path
     * @return {*}
     */
    private function get_meta_path($path){
        if (!is_dir($path)) {
            $path = preg_replace('/(.cbr|.cbz|.zip|.7z|.epub|.rar|.pdf)$/i', '', $path);
        }

        $infoPath = $path . '-smanga-info';

        if (!is_dir($infoPath)) {
            return false;
        }

        return $infoPath;
    }

    /**
     * @description: 获取漫画元数据
     * @param {*} $infoPath
     * @return {*}
     */
    private function get_manga_meta($infoPath)
    {

        $png = $infoPath . '/cover.png';
        $PNG = $infoPath . '/cover.PNG';
        $jpg = $infoPath . '/cover.jpg';
        $JPG = $infoPath . '/cover.JPG';

        // 获取info信息内的封面
        if (is_file($png)) $cover = $png;
        elseif (is_file($PNG)) $cover = $PNG;
        elseif (is_file($jpg)) $cover = $jpg;
        elseif (is_file($JPG)) $cover = $JPG;
        else $cover = '';

        $json = file_get_contents($infoPath . '/info.json');
        $json = json_decode($json);

        // 返回info结果集
        return [
            'title' => $json->title,
            'author' => $json->author,
            'star' => $json->star,
            'describe' => $json->describe,
            'publishDate' => $json->publishDate,
            'mangaCover' => $cover,
        ];
    }

    /**
     * @description: 从元数据获取漫画角色
     * @param {*} $infoPath
     * @return {*}
     */
    private function get_manga_character($infoPath){
        $json = file_get_contents($infoPath . '/info.json');
        $json = json_decode($json);

        if(!$json->character){
            return [];
        }

        return $json->character;
    }

    /**
     * @description: 从元数据获取漫画标签
     * @param {*} $infoPath
     * @return {*}
     */
    private function get_manga_tags($infoPath){
        $json = file_get_contents($infoPath . '/info.json');
        $json = json_decode($json);

        if (!$json->tags) {
            return [];
        }

        return $json->tags;
    }
}

class MangaInfo
{
    public $title;
    public $author;
    public $star;
    public $describe;
    public $cover;
    public $tags;
    public $character;

    public function __construct($data)
    {
        $this->title = $data['title'];
        $this->author = $data['author'];
        $this->star = $data['star'];
        $this->describe = $data['describe'];
        $this->cover = $data['cover'];
        $this->tags = $data['tags'];
        $this->character = $data['character'];
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
