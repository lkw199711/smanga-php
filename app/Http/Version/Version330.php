<?php
/*
* @Author: lkw199711 lkw199711@163.com
* @Date: 2023-11-19 23:23:36
 * @LastEditors: lkw199711 lkw199711@163.com
 * @LastEditTime: 2023-11-20 00:45:58
* @FilePath: /smanga-php/app/Http/Version/Version330.php
*/

namespace App\Http\Version;

use App\Models\VersionSql;
use Illuminate\Support\Facades\DB;

/**
 * @description: sql模型返回数据用
 * @return {*}
 */
class Version330
{
    public function __construct()
    {
        DB::statement("CREATE TABLE IF NOT EXISTS `failed_jobs` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `failed_at` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`) USING BTREE,
            UNIQUE INDEX `failed_jobs_uuid_unique`(`uuid`) USING BTREE
        ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ");

        DB::statement("CREATE TABLE IF NOT EXISTS `jobs` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `attempts` tinyint(3) UNSIGNED NOT NULL,
            `reserved_at` int(10) UNSIGNED NULL DEFAULT NULL,
            `available_at` int(10) UNSIGNED NOT NULL,
            `created_at` int(10) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            INDEX `jobs_queue_index`(`queue`) USING BTREE
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
        ");

        VersionSql::add([
            'version' => '3.3.0',
            'versionDescribe' => '使用laravel重构后端;裁剪模式支持阅读朝向设置;按名称排序按照数字排序方式;新增按id排序.',
            'createTime' => '2023-5-21 23:51:22'
        ]);
    }
}
