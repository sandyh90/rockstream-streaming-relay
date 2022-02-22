<?php

return [
    'ffmpeg_path' => str_replace('/', '\\', env('FFMPEG_PATH', 'bin/ffmpeg/bin')),
    'php_path' => str_replace('/', '\\', env('PHP_PATH', 'bin/php')),
    'nginx_path' => str_replace('/', '\\', env('NGINX_PATH', 'bin/nginx')),
    'nginx_stat_port_rtmp' => env('NGINX_STAT_RTMP_PORT', '7734'),
    'nginx_hls_port_rtmp' => env('NGINX_HLS_RTMP_PORT', '7735'),
    'web_url_checker_rtmp_url' => env('WEB_URL_CHECKER_NGINX_RTMP_URL', 'http://127.0.0.1'),
    'web_url_checker_rtmp_port' => env('WEB_URL_CHECKER_NGINX_RTMP_PORT', '7733'),
    'web_url_checker_rtmp_bypass' => env('WEB_URL_CHECKER_NGINX_RTMP_BYPASS', FALSE),

];
