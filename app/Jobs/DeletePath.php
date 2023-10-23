<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-10-23 12:59:29
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-24 01:28:57
 * @FilePath: /smanga-php/app/Jobs/DeletePath.php
 */

namespace App\Jobs;

use App\Http\Controllers\JobDispatch;
use App\Http\Controllers\Utils;
use App\Models\PathSql;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeletePath implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $pathId;
    private $rescan;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pathId, $rescan = false)
    {
        //
        $this->pathId = $pathId;
        $this->rescan = $rescan;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $mangas = DB::table('manga')->where('pathId', $this->pathId)->get();
        foreach ($mangas as $mangaInfo) {
            JobDispatch::handle('DeleteManga', 'delete', $mangaInfo->mangaId);
        }

        // 当进行重新扫描的时候,不可删除路径
        if (!$this->rescan) {
            // 从数据库删除
            PathSql::path_delete($this->pathId);
        }
    }
}
