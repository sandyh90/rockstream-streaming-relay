<?php

return [
    'ffmpeg_path' => str_replace('/', '\\', env('FFMPEG_PATH', 'bin/ffmpeg/bin')),
    'php_path' => str_replace('/', '\\', env('PHP_PATH', 'bin/php')),
    'nginx_path' => str_replace('/', '\\', env('NGINX_PATH', 'bin/nginx')),
    'nginx_stat_port_rtmp' => env('NGINX_STAT_RTMP_PORT', '7734'),
    'nginx_hls_port_rtmp' => env('NGINX_HLS_RTMP_PORT', '7735'),
    'nginx_url_checker_url' => env('NGINX_URL_CHECKER_URL', 'http://127.0.0.1'),
    'nginx_url_checker_port' => env('NGINX_URL_CHECKER_PORT', '7733'),
    'nginx_url_checker_bypass' => env('NGINX_URL_CHECKER_BYPASS', FALSE),

];
