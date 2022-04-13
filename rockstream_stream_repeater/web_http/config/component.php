<?php

return [
    'ffmpeg_path' => str_contains(strtolower(env('FFMPEG_PATH', 'ffmpeg/bin')), '/') ? str_replace('/', '\\', strtolower(env('FFMPEG_PATH', 'ffmpeg/bin'))) : strtolower(env('FFMPEG_PATH', 'ffmpeg/bin')),
    'php_path' => str_contains(strtolower(env('PHP_PATH', 'php')), '/') ? str_replace('/', '\\', strtolower(env('PHP_PATH', 'php'))) : strtolower(env('PHP_PATH', 'php')),
    'nginx_path' => str_contains(strtolower(env('NGINX_PATH', 'nginx')), '/') ? str_replace('/', '\\', strtolower(env('NGINX_PATH', 'nginx'))) : strtolower(env('NGINX_PATH', 'nginx')),
    'nginx_stat_port_rtmp' => env('NGINX_STAT_RTMP_PORT', '7734'),
    'nginx_hls_port_rtmp' => env('NGINX_HLS_RTMP_PORT', '7735'),
    'web_url_checker_rtmp_url' => env('WEB_URL_CHECKER_NGINX_RTMP_URL', 'http://127.0.0.1'),
    'web_url_checker_rtmp_port' => env('WEB_URL_CHECKER_NGINX_RTMP_PORT', '7733'),
    'web_url_checker_rtmp_bypass' => env('WEB_URL_CHECKER_NGINX_RTMP_BYPASS', FALSE),

];
