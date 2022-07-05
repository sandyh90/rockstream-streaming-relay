![GitHub all releases](https://img.shields.io/github/downloads/sandyh90/rockstream-streaming-relay/total?style=for-the-badge)
![GitHub license](https://img.shields.io/github/license/sandyh90/rockstream-streaming-relay?style=for-the-badge)

<h1 align="center">RockStream Streaming Relay</p>

<h3 align="center">Alternative self hosted live streaming relay to multi platform.</h3>

## Introduction
This live streaming relay or restream application serves to assist you in multi-streaming to several platform locations that you can access in one place to facilitate the live streaming process so you don't have to bother anymore
to create an account with a multi-live streaming service provider, but the this application is only for alternative and not as a substitute for the multi live streaming service provider itself whose stability is guaranteed.

## Features
- Login System
- Multi-Input Stream Location
- Multi-Destination Stream Ingest
- Setup Page
- Livestream Analytics
- Multiple User Auth
- Livestreaming Testing Tool
- Countdown Premiere Video
- Live Preview
- Premiere Video [Beta]

## Server Requirement
- PHP 8.1.4 **Required PHP 8** (Included PHP 8)
- Nginx 1.21.7 (Included Nginx + RTMP Module)
- FFMpeg 5.0 (Included FFMpeg)
- SQLite (Embedded Database)
- High-Speed Internet Min. 10 Mbps and upload speed Min. 5 Mbps or High.
- Processor Min. Dual Core with speed clock 2.40 GHz or High (Except Premiere Video Transcoding).
- Storage free space capacity Min. 1-4 GB or more (Due to live session start the HLS footage will capture temporary for the preview player or you can disable live preview in interfaces settings).
- Use external / dedicated graphics card for best performance (Optional: For Premiere Video Transcoding).

## Limitation
- Due Nginx service being built for the Windows version and some features may not work like in the Linux version.
- Status RTMP in navbar resetting to the beginning if Nginx reloads because of Nginx process use the old process and not automatically use new process, this need **per_worker** listener for now only support in Linux version. [Nginx Patches Source](https://github.com/arut/nginx-patches).
- RTMPS protocol, for now, is not supported on this application and will be supported soon on the new version, but you can try [stunnel](https://www.stunnel.org/) for using RTMPS protocol and some configuration tutorial [How To Use stunnel](https://serverfault.com/questions/1019317/receiving-rtmps-stream-on-nginx-rtmp).
- There are still bugs remaining on the app, Please report any bugs you find.

## Manual Configuration
#### How to install (Manual)
- Extract the ZIP file into a safe folder so it doesn't get mixed up.
- **(Only For Windows Platform)** PHP, FFMPEG, NGINX Binaries already included if you downloaded from [Release Page](https://github.com/sandyh90/rockstream-streaming-relay/releases),
But if you want to run this app on another platform you need change some shell run command and checking file executable on code
- In folder "web_http" change the file ".env.example" to ".env" and change the settings to be as in the .env settings section (APP_KEY is done using artisan)
- First please run the following command " composer install " to install the required dependency libraries.
- Second, please run the command " php artisan key:generate " to generate APP_KEY automatically.
- Thirdly, open the web page control panel to relay live streaming at " http://localhost:7733 ".

#### .env Setting File (Manual)
```
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost
APP_TIMEZONE="UTC"

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

# NGINX RTMP system port and path
NGINX_RTMP_PORT=7734

BROADCAST_DRIVER=log
CACHE_DRIVER=database
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

## Screenshot / Demo

![Login Page](https://user-images.githubusercontent.com/30236529/177349049-2a94e4c5-2716-43a5-b790-8447b93ba54d.jpeg)
![Dashboard Page](https://user-images.githubusercontent.com/30236529/177348420-f726ab42-8f10-4f7c-9c5d-8dcf28be5dae.jpeg)
![Input Stream Page](https://user-images.githubusercontent.com/30236529/177348514-21f56b62-cdc3-4d1c-bc80-0e91e2fd7f70.jpeg)
![Output Stream Page](https://user-images.githubusercontent.com/30236529/177348614-dee23fee-263e-46d5-8fc6-77314547b3e5.jpeg)
![Premiere Video Page](https://user-images.githubusercontent.com/30236529/177348697-879174e0-2dae-4288-9d03-e771da5bc697.jpeg)

## Future Plan
- [ ] Support for linux platform
- [ ] Nginx support RTMPS protocols