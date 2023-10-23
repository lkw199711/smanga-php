<?php

namespace App\Jobs;

use App\Http\Controllers\JobDispatch;
use App\Http\Controllers\Utils;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DeleteMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mediaId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mediaId)
    {
        //
        $this->mediaId = $mediaId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $paths = DB::table('path')->where('mediaId', $this->mediaId)->get();
        foreach ($paths as $pathInfo) {
            // 此处触发删除路径任务
            JobDispatch::handle('DeletePath', 'delete', $pathInfo->pathId);
        }
    }
}
