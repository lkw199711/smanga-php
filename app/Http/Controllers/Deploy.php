<?php

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Models\UserSql;
use App\Models\VersionSql;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Deploy extends Controller
{
    public static function database_test(Request $request)
    {
        $ip = $request->post('ip');
        $userName = $request->post('userName');
        $passWord = $request->post('passWord');
        $database = $request->post('database');
        $port = $request->post('port');

        try {
            mysqli_connect($ip, $userName, $passWord, $database, $port);
            $res = new InterfacesResponse('', '数据库链接成功', 'database link error.');
            return new JsonResponse($res);
        } catch (\Exception $e) {
            return new ErrorResponse('数据库链接错误', 'database link error.', $e->getMessage());
        }
    }
    /**
     * @description: 获取数据库配置
     * @return {*}
     */
    public static function database_get()
    {
        return [
            'code' => 0,
            'ip' => Utils::config_read('sql', 'ip'),
            'userName' => Utils::config_read('sql', 'userName'),
            'passWord' => Utils::config_read('sql', 'passWord'),
            'database' => Utils::config_read('sql', 'database'),
            'port' => Utils::config_read('sql', 'port'),
        ];
    }
    public static function database_set(Request $request)
    {
        $ip = $request->post('ip');
        $userName = $request->post('userName');
        $passWord = $request->post('passWord');
        $database = $request->post('database');
        $port = $request->post('port');

        try {
            mysqli_connect($ip, $userName, $passWord, $database, $port);

            // 写入配置文件
            Utils::config_write('sql', 'ip', $ip);
            Utils::config_write('sql', 'userName', $userName);
            Utils::config_write('sql', 'passWord', $passWord);
            Utils::config_write('sql', 'database', $database);
            Utils::config_write('sql', 'port', $port);

            Utils::update_env([
                'DB_HOST' => $ip,
                'DB_PORT' => $port,
                'DB_DATABASE' => $database,
                'DB_USERNAME' => $userName,
                'DB_PASSWORD' => $passWord
            ]);

            $res = new InterfacesResponse('', '数据库链接成功', 'database link error.');
            return new JsonResponse($res);
        } catch (\Exception $e) {
            return new ErrorResponse('数据库链接错误', 'database link error.', $e->getMessage());
        }
    }
    public static function database_init(Request $request)
    {
        $configPath = Utils::get_env('SMANGA_CONFIG');
        $AppPath = Utils::get_env('SMANGA_APP');
        $installLock = "$configPath/install.lock";
        $versionFile = "$AppPath/version";

        $userName = $request->post('userName');
        $passWord = $request->post('passWord');

        $ip = Utils::config_read('sql', 'ip');
        $sqlUserName = Utils::config_read('sql', 'userName');
        $sqlPassWord = Utils::config_read('sql', 'passWord');
        $database = Utils::config_read('sql', 'database');
        $port = Utils::config_read('sql', 'port');

        // 对于可能不存在的属性 做容错
        if (!$database) {
            $database = 'smanga';
            Utils::config_write('sql', 'database', $database);
        }

        // 3.3.3 新增定时扫描间隔
        if (!Utils::config_read('scan', 'interval ')) {
            Utils::config_write('scan', 'interval', 1 * 24 * 60 * 60);
        }

        // 将原有的config数据库设置写入 env
        Utils::update_env([
            'DB_HOST' => $ip,
            'DB_PORT' => $port,
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $sqlUserName,
            'DB_PASSWORD' => $sqlPassWord
        ]);

        $port = Utils::config_read('sql', 'port');

        $link = @mysqli_connect($ip, $sqlUserName, $sqlPassWord, $database, $port)
            or die(json_encode([
                'code' => 1,
                'initCode' => 0,
                'message' => '数据库链接错误',
            ]));

        // 设置默认字符集
        $link->set_charset('utf8mb4');
        // 切换当前数据库
        $link->query('use smanga;');


        $version = VersionSql::list();
        $vers = [];
        if ($version) {
            $verList = $version;
            for ($i = 0; $i < count($verList); $i++) {
                array_push($vers, $verList[$i]['version']);
            }
        } else {
            $link->query("CREATE TABLE IF NOT EXISTS `bookmark`  (
                `bookmarkId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `mediaId` int(11) NULL DEFAULT NULL,
                `mangaId` int(11) NULL DEFAULT NULL,
                `chapterId` int(11) NULL DEFAULT NULL,
                `userId` int(11) NULL DEFAULT NULL,
                `browseType` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'single',
                `page` int(11) NULL DEFAULT NULL,
                `pageImage` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `createTime` datetime(0) NULL DEFAULT NULL,
                PRIMARY KEY (`bookmarkId`) USING BTREE,
                UNIQUE INDEX `opage`(`chapterId`, `page`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            $link->query("CREATE TABLE IF NOT EXISTS `chapter`  (
                    `chapterId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '章节记录',
                    `mangaId` int(11) NULL DEFAULT NULL COMMENT '漫画id',
                    `mediaId` int(11) NULL DEFAULT NULL COMMENT '媒体库id',
                    `pathId` int(11) NULL DEFAULT NULL COMMENT '路径id',
                    `chapterName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '章节名称',
                    `chapterPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '章节路径',
                    `chapterType` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '文件类型',
                    `browseType` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式',
                    `chapterCover` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '章节封面',
                    `picNum` int(11) NULL DEFAULT NULL COMMENT '图片数量',
                    `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                    `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '最新修改时间',
                    PRIMARY KEY (`chapterId`) USING BTREE,
                    UNIQUE INDEX `oname`(`mangaId`, `chapterName`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
            ");

            $link->query("CREATE TABLE IF NOT EXISTS `compress`  (
                    `compressId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '转换id',
                    `compressType` enum('zip','rar','pdf','image') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '转换类型',
                    `compressPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '转换路径',
                    `compressStatus` enum('uncompressed','compressing','compressed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '转换状态',
                    `createTime` datetime(0) NULL DEFAULT NULL COMMENT '转换时间',
                    `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
                    `imageCount` int(10) NULL DEFAULT NULL COMMENT '图片总数',
                    `mediaId` int(11) NULL DEFAULT NULL COMMENT '媒体库id',
                    `mangaId` int(11) NULL DEFAULT NULL COMMENT '漫画id',
                    `chapterId` int(11) NULL DEFAULT NULL COMMENT '章节id',
                    `chapterPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '章节路径',
                    `userId` int(11) NULL DEFAULT NULL COMMENT '用户标识',
                    PRIMARY KEY (`compressId`) USING BTREE,
                    UNIQUE INDEX `id`(`compressId`) USING BTREE,
                    UNIQUE INDEX `oChapter`(`chapterId`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");
            $link->query("CREATE TABLE IF NOT EXISTS `history`  (
                    `historyId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '历史记录id',
                    `userId` int(11) NULL DEFAULT NULL COMMENT '用户id',
                    `mediaId` int(11) NULL DEFAULT NULL COMMENT '媒体库id',
                    `mangaId` int(11) NULL DEFAULT NULL COMMENT '漫画id',
                    `mangaName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '漫画名称',
                    `chapterId` int(11) NULL DEFAULT NULL COMMENT '章节id',
                    `chapterName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '章节名称',
                    `chapterPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '章节路径',
                    `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                    PRIMARY KEY (`historyId`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");
            $link->query("CREATE TABLE IF NOT EXISTS `login`  (
                    `loginId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '登录记录',
                    `userId` int(11) NULL DEFAULT NULL COMMENT '用户记录',
                    `userName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '用户名',
                    `nickName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '昵称',
                    `request` int(1) NULL DEFAULT NULL COMMENT '0->成功 1->失败',
                    `ip` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'ip地址',
                    `loginTime` datetime(0) NULL DEFAULT NULL COMMENT '登录时间',
                    PRIMARY KEY (`loginId`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");
            $link->query("CREATE TABLE IF NOT EXISTS `manga`  (
                    `mangaId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '漫画id',
                    `mediaId` int(11) NOT NULL COMMENT '媒体库id',
                    `pathId` int(11) NULL DEFAULT NULL COMMENT '路径id',
                    `mangaName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '漫画名称',
                    `mangaPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '漫画路径',
                    `mangaCover` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '漫画封面',
                    `chapterCount` int(191) NULL DEFAULT NULL COMMENT '章节总数',
                    `browseType` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式',
                    `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                    `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '最后修改时间',
                    `direction` int(1) NULL DEFAULT 1 COMMENT '翻页方向 0 左到右; 1右到左',
                    `removeFirst` int(1) NULL DEFAULT 0 COMMENT '剔除首页 01',
                    PRIMARY KEY (`mangaId`) USING BTREE,
                    UNIQUE INDEX `oname`(`mediaId`, `mangaPath`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
            ");
            $link->query("CREATE TABLE IF NOT EXISTS `media`  (
                    `mediaId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '媒体库id',
                    `mediaName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '媒体库名称',
                    `mediaType` int(1) NOT NULL COMMENT '媒体库类型 0->漫画 1->单本',
                    `mediaCover` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '媒体库封面',
                    `directoryFormat` int(1) NULL DEFAULT NULL COMMENT '目录格式 \r\n0 漫画 -> 章节 -> 图片\r\n1 目录 -> 漫画 -> 章节 -> 图片\r\n2 漫画 -> 图片\r\n3 目录 -> 漫画 -> 图片\r\n\r\n23为单本',
                    `fileType` int(1) NULL DEFAULT NULL COMMENT '文件类型 0->图片 1->pdf',
                    `defaultBrowse` enum('flow','single','double') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'flow' COMMENT '默认浏览类型',
                    `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                    `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '最新修改时间',
                    `direction` int(1) NULL DEFAULT 1 COMMENT '翻页方向 0 左到右; 1右到左',
                    `removeFirst` int(1) NULL DEFAULT 0 COMMENT '剔除首页 01',
                    PRIMARY KEY (`mediaId`) USING BTREE,
                    UNIQUE INDEX `nameId`(`mediaId`, `mediaName`) USING BTREE,
                    UNIQUE INDEX `name`(`mediaName`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
            ");
            $link->query("CREATE TABLE IF NOT EXISTS `path`  (
                    `pathId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '路径id',
                    `mediaId` int(11) NOT NULL COMMENT '媒体库id',
                    `path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '路径',
                    `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                    PRIMARY KEY (`pathId`) USING BTREE,
                    UNIQUE INDEX `opath`(`mediaId`, `path`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
            ");
            $link->query("CREATE TABLE IF NOT EXISTS `user`  (
                    `userId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id',
                    `userName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
                    `passWord` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码',
                    `nickName` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '昵称',
                    `registerTime` datetime(0) NULL DEFAULT NULL COMMENT '注册时间',
                    `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '修改时间',
                    PRIMARY KEY (`userId`, `userName`) USING BTREE,
                    UNIQUE INDEX `username`(`userName`) USING BTREE
                ) ENGINE = MyISAM AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;
            ");
            $link->query("CREATE TABLE IF NOT EXISTS `version` (
                `versionId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '版本记录',
                `versionDescribe` VARCHAR(191) NULL DEFAULT NULL COMMENT '版本描述',
                `version` varchar(191) NULL DEFAULT NULL COMMENT 'version number',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT 'createTime',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT 'updateTime',
                PRIMARY KEY (`versionId`) USING BTREE,
                UNIQUE INDEX `version`(`version`) USING BTREE);
            ");

            // 插入smanga的用户名密码
            $link->query("INSERT INTO `user` VALUES (1, 'smanga', 'f7f1fe7186209906a97756ff912bb644', NULL, NULL, NULL);");
        }

        // 插入自定义用户名密码
        if ($userName) {
            if (!$passWord) $passWord = '';
            $passMd5 = md5($passWord);
            UserSql::add(['userName' => $userName, 'passWord' => $passMd5]);
            // $link->query("INSERT INTO `user` VALUES (1, $userName, $passMd5, NULL, NULL, NULL);");
        }

        // 314
        if (array_search('3.1.4', $vers) === false) {
            $link->query("ALTER TABLE compress MODIFY COLUMN compressType enum('zip','rar','pdf','image','7z')");
            VersionSql::add([
                'version' => '3.1.4',
                'versionDescribe' => '添加7z,修复shell参数',
                'createTime' => '2023-2-28 8:32:00'
            ]);
        }

        // 315
        if (array_search('3.1.5', $vers) === false) {
            VersionSql::add([
                'version' => '3.1.5',
                'versionDescribe' => '条漫模式新增书签支持',
                'createTime' => '2023-3-4 14:57:00'
            ]);
        }

        // 316
        if (array_search('3.1.6', $vers) === false) {
            $link->query("ALTER TABLE user ADD `mediaLimit` varchar(191)");
            $link->query("ALTER TABLE user ADD `editMedia` int(1) DEFAULT 1");
            $link->query("ALTER TABLE user ADD `editUser` int(1) DEFAULT 1");

            VersionSql::add([
                'version' => '3.1.6',
                'versionDescribe' => '新增用户权限管理',
                'createTime' => '2023-3-5 18:05:00'
            ]);
        }

        // 317
        if (array_search('3.1.7', $vers) === false) {
            VersionSql::add([
                'version' => '3.1.7',
                'versionDescribe' => '外置sql设置错误问题',
                'createTime' => '2023-3-18 00:27:31'
            ]);
        }

        // 318
        if (array_search('3.1.8', $vers) === false) {
            VersionSql::add([
                'version' => '3.1.8',
                'versionDescribe' => '新增视图切换功能, 解决文字展示不全的问题',
                'createTime' => '2023-4-1 13:23:08'
            ]);
        }

        // 319
        if (array_search('3.1.9', $vers) === false) {
            VersionSql::add([
                'version' => '3.1.9',
                'versionDescribe' => '新增排序方式切换功能',
                'createTime' => '2023-4-1 23:33:43'
            ]);
        }

        // 320
        if (array_search('3.2.0', $vers) === false) {
            VersionSql::add([
                'version' => '3.2.0',
                'versionDescribe' => '新增搜索功能;处理扫描错误.',
                'createTime' => '2023-4-5 21:02:03'
            ]);
        }

        // 321
        if (array_search('3.2.1', $vers) === false) {
            // 创建个人设置表

            $link->query("CREATE TABLE IF NOT EXISTS `config` (
                    `configId` int(0) NOT NULL AUTO_INCREMENT COMMENT '设置项主键',
                    `userId` int(0) NULL COMMENT '关联的用户id',
                    `userName` varchar(191) NULL COMMENT '关联的用户名',
                    `configValue` text NULL COMMENT '设置的详细内容 json打包',
                    `createTime` datetime(0) NULL COMMENT '设置的创建时间',
                    `updateTime` datetime(0) NULL COMMENT '设置的最近升级时间',
                    PRIMARY KEY (`configId`),
                    UNIQUE INDEX `id`(`userId`) USING BTREE COMMENT '用户id唯一'
                )
            ");

            VersionSql::add([
                'version' => '3.2.1',
                'versionDescribe' => '新增用户设置模块',
                'createTime' => '2023-4-22 18:12:57'
            ]);
        }

        // 322
        if (array_search('3.2.2', $vers) === false) {
            VersionSql::add([
                'version' => '3.2.2',
                'versionDescribe' => '修复缓存与排序的bug',
                'createTime' => '2023-4-22 23:49:03'
            ]);
        }

        // 323
        if (array_search('3.2.3', $vers) === false) {
            // 创建个人收藏表
            $link->query("CREATE TABLE IF NOT EXISTS `collect` (
                `collectId` int(0) NOT NULL AUTO_INCREMENT COMMENT '收藏id',
                `collectType` varchar(255) NULL COMMENT '收藏类型',
                `userId` int(0) NOT NULL COMMENT '用户id',
                `mediaId` int(0) NULL COMMENT '媒体库id',
                `mangaId` int(0) NULL COMMENT '漫画id',
                `mangaName` varchar(255) NULL COMMENT '漫画名称',
                `chapterId` int(0) NULL COMMENT '章节id',
                `chapterName` varchar(255) NULL COMMENT '章节名称',
                `createTime` datetime(0) NULL COMMENT '收藏日期',
                PRIMARY KEY (`collectId`),
                UNIQUE INDEX `uManga`(`collectType`, `mangaId`) USING BTREE COMMENT '漫画id不允许重复',
                UNIQUE INDEX `uChapter`(`collectType`, `chapterId`) USING BTREE COMMENT '章节id不允许重复')
            ");

            VersionSql::add([
                'version' => '3.2.3',
                'versionDescribe' => '新增收藏模块',
                'createTime' => '2023-4-24 23:36:57'
            ]);
        }
        // 324
        if (array_search('3.2.4', $vers) === false) {
            // 修改搜索表varchar字段 字符集为utf8mb4
            $link->query("ALTER TABLE `manga` 
                MODIFY COLUMN `mangaName` varchar(191) CHARACTER SET utf8mb4 NOT NULL COMMENT '漫画名称' AFTER `pathId`,
                MODIFY COLUMN `mangaPath` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '漫画路径' AFTER `mangaName`,
                MODIFY COLUMN `mangaCover` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '漫画封面' AFTER `mangaPath`;
            ");

            $link->query("ALTER TABLE `chapter` 
                MODIFY COLUMN `chapterName` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '章节名称' AFTER `pathId`,
                MODIFY COLUMN `chapterPath` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '章节路径' AFTER `chapterName`,
                MODIFY COLUMN `chapterType` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '文件类型' AFTER `chapterPath`,
                MODIFY COLUMN `chapterCover` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '章节封面' AFTER `browseType`;
            ");

            $link->query("ALTER TABLE `media` 
                MODIFY COLUMN `mediaName` varchar(191) CHARACTER SET utf8mb4 NOT NULL COMMENT '媒体库名称' AFTER `mediaId`,
                MODIFY COLUMN `mediaCover` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '媒体库封面' AFTER `mediaType`;
            ");

            $link->query("ALTER TABLE `path` MODIFY COLUMN `path` varchar(191) CHARACTER SET utf8mb4 NOT NULL COMMENT '路径' AFTER `mediaId`;");

            $link->query("ALTER TABLE `user` 
                MODIFY COLUMN `userName` varchar(191) CHARACTER SET utf8mb4 NOT NULL COMMENT '用户名' AFTER `userId`,
                MODIFY COLUMN `nickName` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL COMMENT '昵称' AFTER `passWord`,
                MODIFY COLUMN `mediaLimit` varchar(191) CHARACTER SET utf8mb4 NULL DEFAULT NULL AFTER `updateTime`;
            ");

            VersionSql::add([
                'version' => '3.2.4',
                'versionDescribe' => '适配表情文字',
                'createTime' => '2023-5-1 02:13:55'
            ]);
        }
        // 325
        if (array_search('3.2.5', $vers) === false) {
            VersionSql::add([
                'version' => '3.2.5',
                'versionDescribe' => '修改初始化流程',
                'createTime' => '2023-5-1 11:45:45'
            ]);
        }
        // 326
        if (array_search('3.2.6', $vers) === false) {
            $link->query("ALTER TABLE bookmark MODIFY COLUMN `browseType` enum('flow','single','double','half') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'single' AFTER `userId`;");
            $link->query("ALTER TABLE chapter MODIFY COLUMN `browseType` enum('flow','single','double','half') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式' AFTER `chapterType`;");
            $link->query("ALTER TABLE manga MODIFY COLUMN `browseType` enum('flow','single','double','half') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'flow' COMMENT '浏览方式' AFTER `chapterCount`;");
            $link->query("ALTER TABLE media MODIFY COLUMN `defaultBrowse` enum('flow','single','double','half') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'flow' COMMENT '默认浏览类型' AFTER `fileType`;");

            VersionSql::add([
                'version' => '3.2.6',
                'versionDescribe' => '新增对半裁切模式',
                'createTime' => '2023-5-3 23:49:36'
            ]);
        }
        // 327
        if (array_search('3.2.7', $vers) === false) {
            VersionSql::add([
                'version' => '3.2.7',
                'versionDescribe' => '修正裁切尺寸',
                'createTime' => '2023-5-7 13:25:12'
            ]);
        }
        // 328
        if (array_search('3.2.8', $vers) === false) {
            VersionSql::add([
                'version' => '3.2.8',
                'versionDescribe' => '新增图片下载功能',
                'createTime' => '2023-5-8 00:09:00'
            ]);
        }
        // 329
        if (array_search('3.2.9', $vers) === false) {
            VersionSql::add([
                'version' => '3.2.9',
                'versionDescribe' => '新增分页模式图片缓存,再次加载图片速度会加快.',
                'createTime' => '2023-5-12 22:51:22'
            ]);
        }
        // 330
        if (array_search('3.3.0', $vers) === false) {
            // 生成任务队列表
            $link->query("CREATE TABLE IF NOT EXISTS `failed_jobs` (
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

            $link->query("CREATE TABLE IF NOT EXISTS `jobs` (
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

        // 331
        if (array_search('3.3.1', $vers) === false) {
            // 创建长连接表
            $link->query("CREATE TABLE IF NOT EXISTS `socket` (
                `socketId` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `fd` int(11) NOT NULL,
                `userId` int(11) NULL DEFAULT NULL,
                `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `createTime` datetime(0) NULL DEFAULT NULL,
                `updateTime` datetime(0) NULL DEFAULT NULL,
                PRIMARY KEY (`socketId`, `fd`) USING BTREE
                ) ENGINE = MEMORY AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            // 创建消息表
            $link->query("CREATE TABLE IF NOT EXISTS `notice` (
                `noticeId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `userId` int(11) NULL DEFAULT NULL,
                `code` int(1) NULL DEFAULT NULL,
                `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `createTime` datetime(0) NULL DEFAULT NULL,
                `updateTime` datetime(0) NULL DEFAULT NULL,
                PRIMARY KEY (`noticeId`) USING BTREE
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            // 新增自动扫描字段
            $link->query("ALTER TABLE `path` 
                ADD COLUMN `autoScan` int(1) ZEROFILL NULL COMMENT '自动扫描' AFTER `path`,
                ADD COLUMN `include` varchar(255) NULL COMMENT '包含匹配' AFTER `autoScan`,
                ADD COLUMN `exclude` varchar(255) NULL COMMENT '排除匹配' AFTER `include`,
                ADD COLUMN `updateTime` datetime(0) NULL COMMENT '更新时间' AFTER `createTime`;
            ");

            // 新增3.3.1版本记录
            VersionSql::add([
                'version' => '3.3.1',
                'versionDescribe' => '使用websocket进行消息通知.',
                'createTime' => '2023-5-22 21:05:00'
            ]);
        }

        //332
        if (array_search('3.3.2', $vers) === false) {
            $link->query("CREATE TABLE IF NOT EXISTS `log`  (
                `logId` int(0) UNSIGNED NOT NULL AUTO_INCREMENT,
                `logType` varchar(255) NOT NULL DEFAULT 'process' COMMENT '日志类型 error/process/operate',
                `logLevel` int(0) NULL DEFAULT 0 COMMENT '日志等级',
                `logContent` varchar(255) NULL COMMENT '日志内容',
                `createTime` datetime(0) NULL,
                `updateTime` datetime(0) NULL,
                PRIMARY KEY (`logId`)
            );");

            // 新增3.3.2版本记录
            VersionSql::add([
                'version' => '3.3.2',
                'versionDescribe' => '新增日志模块',
                'createTime' => '2023-6-12 22:16:00'
            ]);
        }

        // 333
        if (array_search('3.3.3', $vers) === false) {
            $link->query("CREATE TABLE IF NOT EXISTS `scan`  (
                `scanId` int(0) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
                `scanStatus` varchar(255) NULL COMMENT 'start|scaning|finish',
                `path` varchar(255) NULL COMMENT '路径',
                `targetPath` varchar(255) NULL COMMENT '正在扫描的二级路径',
                `pathId` int(0) NOT NULL COMMENT '第二主键',
                `scanCount` int(0) NULL COMMENT '扫描目标总数',
                `scanIndex` int(0) NULL COMMENT '正在扫描的进度',
                `createTime` datetime(0) NULL COMMENT '扫描任务开始时间',
                `updateTime` datetime(0) NULL COMMENT '扫描任务更新时间',
                PRIMARY KEY (`scanId`, `pathId`)
              ) ENGINE = MEMORY;");

            // 新增定时扫描字段
            $link->query("ALTER TABLE `path` 
                ADD COLUMN `scheduledScan` int(1) ZEROFILL NULL COMMENT '定时扫描' AFTER `autoScan`,
                ADD COLUMN `lastScanTime` datetime(0) NULL COMMENT '上次扫描时间' AFTER `exclude`;
            ");

            // 新增3.3.3版本记录
            VersionSql::add([
                'version' => '3.3.3',
                'versionDescribe' => '扫描系统做节流处理,正在进行扫描的目录不再接收扫描任务',
                'createTime' => '2023-6-13 20:52:00'
            ]);
        }

        // 334
        if (array_search('3.3.4', $vers) === false) {
            // 新增3.3.4版本记录
            VersionSql::add([
                'version' => '3.3.4',
                'versionDescribe' => '新增自动扫描时间设置',
                'createTime' => '2023-07-16 17:34:00'
            ]);
        }

        // 337
        if (array_search('3.3.7', $vers) === false) {
            // 创建标签表
            $link->query("CREATE TABLE IF NOT EXISTS `tag`  (
                `tagId` int(0) NOT NULL AUTO_INCREMENT COMMENT '标签主键',
                `tagName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签名称',
                `tagColor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签颜色',
                `userId` int(0) NULL DEFAULT NULL COMMENT '用户id',
                `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标签说明',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '升级时间',
                PRIMARY KEY (`tagId`) USING BTREE,
                UNIQUE INDEX `o`(`tagName`, `userId`) USING BTREE COMMENT '标签唯一'
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            // 创建漫画标签表
            $link->query("CREATE TABLE IF NOT EXISTS `mangaTag`  (
                `mangaTagId` int(0) NOT NULL AUTO_INCREMENT COMMENT '漫画关联标签主键',
                `mangaId` int(0) NULL DEFAULT NULL COMMENT '漫画id',
                `tagId` int(0) NULL DEFAULT NULL COMMENT '标签id',
                `createTime` datetime(0) NULL DEFAULT NULL COMMENT '创建时间',
                `updateTime` datetime(0) NULL DEFAULT NULL COMMENT '更新时间',
                PRIMARY KEY (`mangaTagId`) USING BTREE,
                UNIQUE INDEX `manga-tag`(`mangaId`, `tagId`) USING BTREE COMMENT '相同的标签不能多次添加'
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            // 新增3.3.7版本记录
            VersionSql::add([
                'version' => '3.3.7',
                'versionDescribe' => '新增自定义标签功能',
                'createTime' => '2023-07-29 16:03:00'
            ]);
        }

        // 338
        if (array_search('3.3.8', $vers) === false) {
            // 创建角色表
            $link->query("CREATE TABLE IF NOT EXISTS `character`  (
                    `characterId` int(0) NOT NULL AUTO_INCREMENT,
                    `characterName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                    `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                    `characterPicture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                    `createTime` datetime(0) NULL DEFAULT NULL,
                    `updateTime` datetime(0) NULL DEFAULT NULL,
                    `mangaId` int(0) NULL DEFAULT NULL,
                    PRIMARY KEY (`characterId`) USING BTREE,
                    UNIQUE INDEX `o`(`characterName`, `mangaId`) USING BTREE COMMENT '同一漫画不能有重名的角色'
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            // 创建元数据表
            $link->query("CREATE TABLE IF NOT EXISTS `meta`  (
                `metaId` int(0) NOT NULL AUTO_INCREMENT,
                `metaType` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `mangaId` int(0) NULL DEFAULT NULL,
                `metaFile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `metaContent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `createTime` datetime(0) NULL DEFAULT NULL,
                `updateTime` datetime(0) NULL DEFAULT NULL,
                PRIMARY KEY (`metaId`) USING BTREE,
                UNIQUE INDEX `o`(`mangaId`, `metaFile`) USING BTREE COMMENT '同个漫画的某个元数据不能引入两次'
                ) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            // 在manga表中新增元数据字段
            $link->query("ALTER TABLE `manga` 
                ADD COLUMN `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '元数据标题',
                ADD COLUMN `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '作者',
                ADD COLUMN `star` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '评价',
                ADD COLUMN `describe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '作品简介',
                ADD COLUMN `publishDate` date NULL DEFAULT NULL COMMENT '发布日期';
            ");

            // 新增3.3.8版本记录
            VersionSql::add([
                'version' => '3.3.8',
                'versionDescribe' => '新增元数据刮削功能',
                'createTime' => '2023-08-18 03:15:00'
            ]);
        }

        // 339
        if (array_search('3.3.9', $vers) === false) {
            // 新增3.3.9版本记录
            VersionSql::add([
                'version' => '3.3.9',
                'versionDescribe' => '漫画管理新增搜索框',
                'createTime' => '2023-08-25 20:19:00'
            ]);
        }

        // 340
        if (array_search('3.4.0', $vers) === false) {
            // 新增3.4.0版本记录
            VersionSql::add([
                'version' => '3.4.0',
                'versionDescribe' => '自动解压设置项',
                'createTime' => '2023-08-26 06:33:00'
            ]);
        }

        // 341
        if (array_search('3.4.1', $vers) === false) {
            // 新增3.4.1版本记录
            VersionSql::add([
                'version' => '3.4.1',
                'versionDescribe' => '临时增加登出按钮',
                'createTime' => '2023-09-13 02:07:00'
            ]);
        }

        // 342
        if (array_search('3.4.2', $vers) === false) {
            // 新增3.4.2版本记录
            VersionSql::add([
                'version' => '3.4.2',
                'versionDescribe' => '移动端滑动翻页功能',
                'createTime' => '2023-09-13 20:30:00'
            ]);
        }

        // 343
        if (array_search('3.4.3', $vers) === false) {
            // 新增3.4.3版本记录
            VersionSql::add([
                'version' => '3.4.3',
                'versionDescribe' => '章节管理新增搜索框',
                'createTime' => '2023-09-14 10:55:00'
            ]);
        }

        // 344
        if (array_search('3.4.4', $vers) === false) {
            // 新增3.4.4版本记录
            VersionSql::add([
                'version' => '3.4.4',
                'versionDescribe' => '修复解压路径获取错误的问题',
                'createTime' => '2023-09-21 08:16:00'
            ]);
        }

        // 345
        if (array_search('3.4.5', $vers) === false) {
            // 新增3.4.5版本记录
            VersionSql::add([
                'version' => '3.4.5',
                'versionDescribe' => '漫画详情页面新增收藏按钮',
                'createTime' => '2023-09-23 13:52:00'
            ]);
        }

        // 346
        if (array_search('3.4.6', $vers) === false) {
            // 新增3.4.6版本记录
            VersionSql::add([
                'version' => '3.4.6',
                'versionDescribe' => 'pageSize逻辑调整',
                'createTime' => '2023-09-24 12:16:00'
            ]);
        }

        // 347
        if (array_search('3.4.7', $vers) === false) {
            // 新增3.4.7版本记录
            VersionSql::add([
                'version' => '3.4.7',
                'versionDescribe' => '切换章节错误bug修复',
                'createTime' => '2023-09-24 23:30:00'
            ]);
        }

        // 348
        if (array_search('3.4.8', $vers) === false) {
            // 生成任务队列表
            $link->query("CREATE TABLE IF NOT EXISTS `lastRead` (
                `lastReadId` int UNSIGNED NOT NULL AUTO_INCREMENT,
                `page` int(0) NOT NULL,
                `chapterId` int(0) NOT NULL,
                `mangaId` int(0) NOT NULL,
                `userId` int(0) NOT NULL,
                `createTime` datetime(0) NULL DEFAULT NULL,
                `updateTime` datetime(0) NULL DEFAULT NULL,
                PRIMARY KEY (`lastReadId`) USING BTREE
                ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;
            ");

            // 新增3.4.8版本记录
            VersionSql::add([
                'version' => '3.4.8',
                'versionDescribe' => '新增"继续阅读模块",准确定位"继续阅读"功能.',
                'createTime' => '2023-10-08 19:33:16'
            ]);
        }

        // 349
        if (array_search('3.4.9', $vers) === false) {
            // 生成任务队列表
            $link->query("ALTER TABLE `lastRead` 
                ADD COLUMN `finish` int(1) ZEROFILL NOT NULL DEFAULT 0 COMMENT '已完成阅读' AFTER `page`;
            ");

            // 新增3.4.9版本记录
            VersionSql::add([
                'version' => '3.4.9',
                'versionDescribe' => '修复列表视图无法上下滚动的问题.',
                'createTime' => '2023-10-12 21:40:21'
            ]);
        }

        // 350
        if (array_search('3.5.0', $vers) === false) {
            // 新增3.5.0版本记录
            VersionSql::add([
                'version' => '3.5.0',
                'versionDescribe' => '菜单分类并修改图表.',
                'createTime' => '2023-10-18 23:47:35'
            ]);
        }

        // 351
        if (array_search('3.5.1', $vers) === false) {
            // 新增3.5.1版本记录
            VersionSql::add([
                'version' => '3.5.1',
                'versionDescribe' => '扫描时自动提取封面.',
                'createTime' => '2023-10-20 15:03:39'
            ]);
        }

        // 352
        if (array_search('3.5.2', $vers) === false) {
            // 新增3.5.2版本记录
            VersionSql::add([
                'version' => '3.5.2',
                'versionDescribe' => '目录扫描方法替换.',
                'createTime' => '2023-10-21 18:45:52'
            ]);
        }

        if (array_search('3.5.3', $vers) === false) {
            // 新增3.5.3版本记录
            VersionSql::add([
                'version' => '3.5.3',
                'versionDescribe' => '散图漫画封面删除bug修复.',
                'createTime' => '2023-10-21 21:19:15'
            ]);
        }

        if (array_search('3.5.4', $vers) === false) {
            VersionSql::add([
                'version' => '3.5.4',
                'versionDescribe' => '新增漫画排序逻辑.',
                'createTime' => '2023-10-22 16:00:38'
            ]);
        }

        if (array_search('3.5.5', $vers) === false) {
            VersionSql::add([
                'version' => '3.5.5',
                'versionDescribe' => '散图获取封面bug修复.',
                'createTime' => '2023-10-22 19:34:56'
            ]);
        }

        if (array_search('3.5.7', $vers) === false) {
            // 生成任务队列表
            $link->query("ALTER TABLE `log` 
                ADD COLUMN `errorMessage` text NULL COMMENT '代码错误日志' AFTER `updateTime`;
            ");

            VersionSql::add([
                'version' => '3.5.7',
                'versionDescribe' => '优化代码,增加图片错误处理.',
                'createTime' => '2023-10-26 21:58:16'
            ]);
        }

        if (array_search('3.6.0', $vers) === false) {
            VersionSql::add([
                'version' => '3.6.0',
                'versionDescribe' => '封面图片缓存,添加骨架屏动画.',
                'createTime' => '2023-11-01 01:08:17'
            ]);
        }
        // 有此文件说明并非初次部署
        Utils::write_txt("$configPath/install.lock", 'success');

        if (is_file($installLock)) {
            // 记录版本 代表初始化结束
            Utils::write_txt($versionFile, '3.5.4');
        }

        // 重启守护进程与队列服务
        shell_exec('supervisorctl restart all');

        return [
            'code' => 0, 'vers' => $vers, 'message' => '初始化成功!'
        ];
    }
}
