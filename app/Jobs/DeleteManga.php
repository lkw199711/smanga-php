<?php
/*
 * @Author: lkw199711 lkw199711@163.com
 * @Date: 2023-10-23 13:07:51
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-10-23 13:09:05
 * @FilePath: /smanga-php/app/Jobs/DeleteManga.php
 */

namespace App\Jobs;

use App\Models\MangaSql;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteManga implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mangaId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mangaId)
    {
        //
        $this->mangaId = $mangaId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        MangaSql::manga_delete($this->mangaId);
    }
}
