<?php

namespace App\Component;

use App\Models\StreamInput;

define('T', "\t");
define('N', "\n");

class NginxConfigGen
{
    public static function GenerateRTMPIngest()
    {
        # Generate RTMP ingest config.
        $rtmp_cfg = "";
        foreach (StreamInput::with('ingest_destination_data')->where('active_input_stream', TRUE)->get() as $stream) {
            $rtmp_cfg .= T . T . 'application ' . $stream->name_input_stream . ' {' . N;
            $rtmp_cfg .= T . T . T . 'live on;' . N;
            $rtmp_cfg .= T . T . T . 'record off;' . N;
            $rtmp_cfg .= T . T . T . 'meta copy;' . N;

            if ($stream->ingest_destination_data->count() > 0) {
                foreach ($stream->ingest_destination_data as $stream) {
                    if ($stream->active_stream_dest == TRUE) {
                        $rtmp_cfg .= T . T . T . "push " . self::URLBuilderRTMP($stream->url_stream_dest, $stream->key_stream_dest) . ";" . N;
                    }
                }
            }

            $rtmp_cfg .= T . T . '}' . N;
        }

        return $rtmp_cfg;
    }

    # generate nginx config for a site
    public static function GenerateBaseConfig()
    {
        # nginx config folder
        $nginx_file = (Utility::defaultBinDirFolder(config('component.nginx_path')) . '\conf');

        # if the file doesn't exist and can't be created, throw an error and exit the script.
        if (!file_exists($nginx_file)) {
            if (!mkdir($nginx_file, 0777, true) || !is_dir($nginx_file)) {
                throw new \Exception('Failed to create folders...');
            }
        }

        # Nginx config base template file path and name (relative to this file)
        $base_nginx = '';
        $base_nginx .= 'worker_processes  1;' . N;
        $base_nginx .= N . N;
        $base_nginx .= 'events {' . N;
        $base_nginx .=      T . 'worker_connections  1024; # max number of connections per worker process (default 1024)' . N;
        $base_nginx .=      T . 'multi_accept on; # accept multiple connections at once (default off)' . N;
        $base_nginx .= '}' . N;
        $base_nginx .= N . N;
        $base_nginx .= '# RTMP configuration' . N;
        $base_nginx .= 'rtmp {' . N;
        $base_nginx .=      T . 'server {' . N;
        $base_nginx .=          T . T . 'listen 1935;' . N;
        $base_nginx .=          T . T . 'chunk_size 4096;' . N;
        $base_nginx .= N;
        $base_nginx .=          T . T . 'on_publish ' . (config('component.web_url_checker_rtmp_bypass') == TRUE ? config('app.url') : config('component.web_url_checker_rtmp_url')) . ':' . config('component.web_url_checker_rtmp_port')  . '/api/stream/on_publish;' . N;
        $base_nginx .=          T . T . 'on_publish_done ' . (config('component.web_url_checker_rtmp_bypass') == TRUE ? config('app.url') : config('component.web_url_checker_rtmp_url')) . ':' . config('component.web_url_checker_rtmp_port')  . '/api/stream/on_publish_done;' . N;
        $base_nginx .=          T . T . '# Turn on HLS' . N;
        $base_nginx .=          T . T . 'hls on;' . N;
        $base_nginx .=          T . T . 'hls_path hls/;' . N;
        $base_nginx .=          T . T . 'hls_fragment 30;' . N;
        $base_nginx .=          T . T . 'hls_playlist_length 10;' . N;
        $base_nginx .=          T . T . 'hls_cleanup on;' . N;
        $base_nginx .=          T . T . 'hls_nested on;' . N;
        $base_nginx .=          T . T . '# disable consuming the stream from nginx as rtmp' . N;
        $base_nginx .=          T . T . 'deny play all;' . N;
        $base_nginx .= N;
        $base_nginx .=          self::GenerateRTMPIngest();
        $base_nginx .=      T . '}' . N;
        $base_nginx .= '}' . N;
        $base_nginx .= N . N;
        $base_nginx .= 'http {' . N;
        $base_nginx .=      T . 'include mime.types;' . N;
        $base_nginx .=      T . 'default_type application/octet-stream;' . N;
        $base_nginx .=      T . 'index index.php index.html index.htm;' . N;
        $base_nginx .=      T . 'server {' . N;
        $base_nginx .=          T . T . 'listen ' . config('component.nginx_stat_port_rtmp') . ';' . N;
        $base_nginx .=          T . T . 'location / {' . N;
        $base_nginx .=          T . T . T . 'rtmp_stat all;' . N;
        $base_nginx .=          T . T . T . 'rtmp_stat_stylesheet stat.xsl;' . N;
        $base_nginx .=          T . T . '}' . N;
        $base_nginx .=          T . T . T . 'location /stat.xsl {' . N;
        $base_nginx .=          T . T . T . 'root html/;' . N;
        $base_nginx .=          T . T . '}' . N;
        $base_nginx .=      T . '}' . N;
        $base_nginx .=      N;
        $base_nginx .=      T . 'server {' . N;
        $base_nginx .=          T . T . 'listen ' . config('component.nginx_hls_port_rtmp') . ';' . N;
        $base_nginx .=          T . T . 'location / {' . N;
        $base_nginx .=                  T . T . T . '# Disable cache' . N;
        $base_nginx .=                  T . T . T . "add_header 'Cache-Control' 'no-cache';" . N;
        $base_nginx .=                  T . T . T . 'root hls/;' . N;
        $base_nginx .=                  T . T . T . '# CORS setup' . N;
        $base_nginx .=                  T . T . T . "add_header 'Access-Control-Allow-Origin' '*' always;" . N;
        $base_nginx .=                  T . T . T . "add_header 'Access-Control-Expose-Headers' 'Content-Length';" . N;
        $base_nginx .=                  T . T . T . "# allow CORS preflight requests" . N;
        $base_nginx .=                  T . T . T . 'if ($request_method = "OPTIONS") {' . N;
        $base_nginx .=                  T . T . T . "add_header 'Access-Control-Allow-Origin' '*';" . N;
        $base_nginx .=                  T . T . T . "add_header 'Access-Control-Max-Age' 1728000;" . N;
        $base_nginx .=                  T . T . T . "add_header 'Content-Type' 'text/plain charset=UTF-8';" . N;
        $base_nginx .=                  T . T . T . "add_header 'Content-Length' 0;" . N;
        $base_nginx .=                  T . T . T . 'return 204;' . N;
        $base_nginx .=                  T . T . T . '}' . N;
        $base_nginx .=                  T . T . T . 'types {' . N;
        $base_nginx .=                  T . T . T . 'application/dash+xml mpd;' . N;
        $base_nginx .=                  T . T . T . 'application/vnd.apple.mpegurl m3u8;' . N;
        $base_nginx .=                  T . T . T . 'video/mp2t ts;' . N;
        $base_nginx .=                  T . T . T . '}' . N;
        $base_nginx .=          T . T . '}' . N;
        $base_nginx .=      T . '}' . N;
        $base_nginx .= '}';

        # Write the file
        file_put_contents($nginx_file . DIRECTORY_SEPARATOR . 'nginx.conf', $base_nginx);
    }

    public static function URLBuilderRTMP($url, $stream_name)
    {
        # Build the URL for RTMP streaming to the client machine.
        $rtmp_url = rtrim($url, '/') . '/' . $stream_name;
        return $rtmp_url;
    }
}
