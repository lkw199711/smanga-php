<?php

namespace App\Http\Controllers;

use App\Http\PublicClass\ErrorResponse;
use App\Http\PublicClass\InterfacesResponse;
use App\Http\Version\Version300;
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

    /**
     * @description: 设置数据链接
     * @param {Request} $request
     * @return {*}
     */
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

    /**
     * @description: 数据库初始化
     * @param {Request} $request
     * @return {*}
     */
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

        // 目前线上项目的版本更新
        $versionList = [
            '3.1.4', '3.1.5', '3.1.6', '3.1.7', '3.1.8', '3.1.9',
            '3.2.0', '3.2.1', '3.2.2', '3.2.3', '3.2.4', '3.2.5', '3.2.6', '3.2.7', '3.2.8', '3.2.9',
            '3.3.0', '3.3.1', '3.3.2', '3.3.3', '3.3.4', '3.3.7', '3.3.8', '3.3.9',
            '3.4.0', '3.4.1', '3.4.2', '3.4.3', '3.4.4', '3.4.5', '3.4.6', '3.4.7', '3.4.8', '3.4.9',
            '3.5.0', '3.5.1', '3.5.2', '3.5.3', '3.5.4', '3.5.5', '3.5.7', '3.6.0',
            '3.6.1', '3.6.2', '3.6.3'
        ];

        $version = VersionSql::list();
        $vers = [];
        if ($version) {
            $verList = $version;
            for ($i = 0; $i < count($verList); $i++) {
                array_push($vers, $verList[$i]['version']);
            }
        } else {
            new Version300();
        }

        // 插入自定义用户名密码
        if ($userName) {
            if (!$passWord) $passWord = '';
            $passMd5 = md5($passWord);
            UserSql::add(['userName' => $userName, 'passWord' => $passMd5]);
        }

        // 检查版本升级
        foreach ($versionList as $value) {
            if (array_search($value, $vers) === false) {
                $verNum = str_replace(".", "", $value);
                $className = "App\Http\Version\Version{$verNum}";
                new $className();
            }
        }

        // 有此文件说明并非初次部署
        Utils::write_txt("$configPath/install.lock", 'success');

        if (is_file($installLock)) {
            // 记录版本 代表初始化结束
            Utils::write_txt($versionFile, $versionList[count($versionList) - 1]);
        }

        // 重启守护进程与队列服务
        shell_exec('supervisorctl restart all');

        $res = new InterfacesResponse($vers, '初始化成功!', 'smanga init successed.');
        return new JsonResponse($res);
    }

    /**
     * @description: ssl证书设置
     * @param {Request} $request
     * @return {*}
     */
    public static function reset_ssl()
    {
        $nginxConfig = <<<NGINX
        server {
            listen 443 default_server;
            listen [::]:443 default_server;

            root /app;
            index index.html index.php;

            location / {
                    try_files \$uri \$uri/ /index.php?\$query_string;
            }

            location /websocket {
                    proxy_pass http://127.0.0.1:9501;
                    proxy_http_version 1.1;
                    proxy_set_header Upgrade \$http_upgrade;
                    proxy_set_header Connection "upgrade";
            }

            location ~ \.php(.*)$ {
                    fastcgi_pass   unix:/run/php/php-fpm.sock;
                    fastcgi_index  index.php;
                    fastcgi_param  SCRIPT_FILENAME  /app/\$fastcgi_script_name;
                    include        fastcgi_params;
            }

            # You may need this to prevent return 404 recursion.
            location = /404.html {
                    internal;
            }
        }
        NGINX;
        // 将配置写入文件
        file_put_contents('/etc/nginx/conf.d/ssl.conf', $nginxConfig);

        // 重载nginx
        shell_exec('nginx -s reload');

        // 将ini设置置空
        Utils::config_write('ssl','pem','');
        Utils::config_write('ssl','key','');

        // 输出结果
        $res = new InterfacesResponse('', 'ssl证书重置成功', 'ssl reset success');
        return new JsonResponse($res);
    }

    /**
     * @description: ssl证书设置
     * @param {Request} $request
     * @return {*}
     */
    public static function set_ssl(Request $request)
    {
        // 获取 PEM 和 KEY 文件路径
        $pemPath = $request->get('pem');
        $keyPath = $request->get('key');

        // 检查文件路径是否有效，省略了错误处理，请根据实际需要添加错误处理逻辑
        if (!file_exists($pemPath) || !file_exists($keyPath)) {
            $res = new InterfacesResponse('', '证书文件路径无效', 'Invalid certificate file paths');
            return new JsonResponse($res);
        }

        // 读取 PEM 和 KEY 文件内容
        $pemContent = file_get_contents($pemPath);
        $keyContent = file_get_contents($keyPath);

        // 写入ini
        Utils::config_write('ssl','pem',$pemPath);
        Utils::config_write('ssl','key',$keyPath);

        // 生成 Nginx 配置
        $nginxConfig = <<<NGINX
        server {
            listen 443 ssl default_server;
            listen [::]:443 ssl default_server;

            ssl_certificate $pemPath;
            ssl_certificate_key $keyPath;

            root /app;
            index index.html index.php;

            location / {
                try_files \$uri \$uri/ /index.php?\$query_string;
            }

            location /websocket {
                proxy_pass http://127.0.0.1:9501;
                proxy_http_version 1.1;
                proxy_set_header Upgrade \$http_upgrade;
                proxy_set_header Connection "upgrade";
            }

            location ~ \.php(.*)$ {
                fastcgi_pass   unix:/run/php/php-fpm.sock;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME  /app/\$fastcgi_script_name;
                include        fastcgi_params;
            }

            # You may need this to prevent return 404 recursion.
            location = /404.html {
                internal;
            }
        }
        NGINX;

        // 将配置写入文件
        file_put_contents('/etc/nginx/conf.d/ssl.conf', $nginxConfig);

        // 重载nginx
        shell_exec('nginx -s reload');

        // 输出结果
        $res = new InterfacesResponse('', 'SSL证书设置成功', 'SSL set success');
        return new JsonResponse($res);
    }
}
