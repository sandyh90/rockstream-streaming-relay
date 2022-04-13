![GitHub release (latest by date)](https://img.shields.io/github/downloads/sandyh90/rockstream-streaming-relay/latest/total?style=flat-square)

<h1 align="center">RockStream Streaming Relay</p>

<h3 align="center">Alternative self hosted live streaming relay to multi platform.</h3>

## Introduction
This live streaming relay or restream application serves to assist you in multi-streaming to several platform locations that you can access in one place to facilitate the live streaming process so you don't have to bother anymore
to create an account with a multi-live streaming service provider, but the this application is only for alternative and not as a substitute for the multi live streaming service provider itself whose stability is guaranteed.

## Features
- Login System
- Multi Input Stream Location
- Multi Destination Stream Ingest
- Setup Page
- Live Preview [Beta]
- Premiere Video [Beta]

## Server Requirement
- PHP 8.1.4 **Required PHP 8** (Included PHP 8)
- Nginx 1.21.7 (Included Nginx)
- FFMpeg 5.0(Included FFMpeg)
- SQLite (Embedded Database)
- High-Speed Internet Min. 10 Mbps and upload speed Min. 5 Mbps or High.
- Processor Min. Dual Core with speed clock 2.40 GHz or High (Except For Premiere Video Transcoding).
- Storage free space capacity Min. 1-4 GB or more (Due to live session start the HLS footage will capture temporary for the preview player).
- (Optional: For Premiere Video Transcoding) Use external / dedicated graphics card for best performance.

## Limitation
- Due Nginx service built for the Windows version and some features may not work like in the Linux version.
- Status RTMP in navbar resetting to the beginning if Nginx reloads because of Nginx process use the old process and not automatically use new process, this need per_worker listener for now only support in Linux version. [Nginx Patches Source](https://github.com/arut/nginx-patches).
- RTMPS protocol, for now, is not supported on this application and will be supported soon on the new version.
- There is still bugs remaining on the app, Please report any bugs you find.

## .env Setting File
```
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# For Nginx process system configuration

# NGINX url rtmp checker and this seperate url from APP_URL because
# sometime domain or url is not always same as APP_URL to communicate with this web application.
WEB_URL_CHECKER_NGINX_RTMP_URL=http://127.0.0.1

# NGINX port rtmp checker and this seperate port if this app need
# to use different port from APP_URL to communicate with this web application. default (7733)
WEB_URL_CHECKER_NGINX_RTMP_PORT=7733

# NGINX will Use APP_URL and ignore WEB_URL_CHECKER_NGINX_RTMP_URL to set url
# check data publish url from client side to server side
WEB_URL_CHECKER_NGINX_RTMP_BYPASS=false

# NGINX RTMP system port and path

NGINX_STAT_RTMP_PORT=7734
NGINX_HLS_RTMP_PORT=7735

NGINX_PATH="nginx"
FFMPEG_PATH="ffmpeg/bin"
PHP_PATH="php"

BROADCAST_DRIVER=log
CACHE_DRIVER=database
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

## Screenshot / Demo


![Login Page](https://user-images.githubusercontent.com/30236529/156620510-0f9a186f-4e6d-48a1-a532-e323bc471131.jpeg)
![Dashboard Page](https://user-images.githubusercontent.com/30236529/156620562-83933922-0077-479f-bcf9-2360f7d3a413.jpeg)
![Input Stream Page](https://user-images.githubusercontent.com/30236529/156620629-b4098974-72e3-4b05-8f0f-71a21491033e.jpeg)
![Output Stream Page](https://user-images.githubusercontent.com/30236529/156620685-23cf7847-d9e7-4650-9f05-08e920ffb70e.jpeg)
![Premiere Video Page](https://user-images.githubusercontent.com/30236529/156620738-bf534d30-28ec-4fdd-b3e0-70e44bb0d24a.jpeg)
