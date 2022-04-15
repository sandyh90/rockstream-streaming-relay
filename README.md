![GitHub release (latest by date)](https://img.shields.io/github/downloads/sandyh90/rockstream-streaming-relay/latest/total?style=for-the-badge)
![GitHub license](https://img.shields.io/github/license/sandyh90/rockstream-streaming-relay?style=for-the-badge)

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
- Livestream Analytics
- Multiple User Auth
- Livestreaming Testing Tool
- Countdown Premiere Video
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

## How to install (Manual)
- Extract the ZIP file into a safe folder so it doesn't get mixed up.
- (For Windows) PHP, FFMPEG, NGINX Binaries already included if you downloaded from [Release Page](https://github.com/sandyh90/rockstream-streaming-relay/releases),
But if you want to run this app on another platform you need change some run shell and checking file executable on code
- In folder "web_http" change the file ".env.example" to ".env" and change the settings to be as in the .env settings section (APP_KEY is done using artisan)
- First please run the following command " composer install " to install the required dependency libraries.
- Second, please run the command " php artisan key:generate " to generate APP_KEY automatically.
- Thirdly, open the web page control panel to relay live streaming at " http://localhost:7733 ".

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
![Dashboard Page](https://user-images.githubusercontent.com/30236529/163223536-2a737e4e-ce1c-461f-9e47-e57230cd78e5.jpeg)
![Input Stream Page](https://user-images.githubusercontent.com/30236529/163223823-bd44fff7-0530-4c9e-b4e9-aefe6d005f1f.jpeg)
![Output Stream Page](https://user-images.githubusercontent.com/30236529/163223908-f4e6927e-ef0b-412f-90ef-106de165a08c.jpeg)
![Premiere Video Page](https://user-images.githubusercontent.com/30236529/163223975-0a03bfb5-3b57-45b0-9416-4a24db6b2b57.jpeg)

