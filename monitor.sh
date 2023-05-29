#!/bin/bash
###
 # @Author: lkw199711 lkw199711@163.com
 # @Date: 2023-05-28 16:10:26
 # @LastEditors: lkw199711 lkw199711@163.com
 # @LastEditTime: 2023-05-29 23:56:49
 # @FilePath: /php/monitor.sh
### 

# 获取传递的目录参数
DIRECTORY="$1"

# PHP脚本路径
PHP_SCRIPT="/path/to/your/php/script.php"

# 检查目录参数是否为空
if [ -z "$DIRECTORY" ]; then
    echo "请提供目录路径作为参数。"
    exit 1
fi

# 使用inotifywait命令监控目录的变化
inotifywait -m -r -e modify,create,delete "$DIRECTORY" |
while read -r directory event filename; do
    # 过滤掉不需要的事件
    # if [[ "$event" = "MODIFY" || "$event" = "CREATE" || "$event" = "DELETE" ]]; then
    #     # 执行PHP脚本
    #     php artisan scan:auto "$directory"
    # fi

    if [[ "$event" = "CREATE" ]]; then
        # 执行PHP脚本
        php artisan scan:auto "$directory"
    fi

    # if [[ "$event" = "MODIFY" || "$event" = "CREATE" || "$event" = "DELETE" ]]; then
    #     # 执行PHP脚本
    #     php artisan scan:auto "$directory"
    # fi
done
