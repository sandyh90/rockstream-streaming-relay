<?php

return [
    'nginx_path' => str_replace('/', '\\', env('NGINX_PATH', 'bin/nginx')),
    'nginx_stat_port_rtmp' => env('NGINX_STAT_RTMP_PORT', '7734'),
    'nginx_hls_port_rtmp' => env('NGINX_HLS_RTMP_PORT', '7735'),
];
