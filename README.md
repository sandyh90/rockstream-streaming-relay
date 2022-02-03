<h1 align="center">RockStream Streaming Relay</p>

<h3 align="center">Alternative self hosted streaming relay to multi platform.</h3>

## Introduction
This live streaming relay application serves to assist you in multi-streaming to several platform locations that you can access in one place to facilitate the live streaming process so you don't have to bother anymore
to create an account with a multi-live streaming service provider, but the this application is only for alternative and not as a substitute for the multi live streaming service provider itself whose stability is guaranteed.

## Features
- Login System
- Multi Input Stream Location
- Multi Destination Stream Ingest
- Setup Page
- Live Preview [Experimental]

## Server Requirement
- PHP 7.4.8 [Support PHP 8] (Included PHP 8)
- Nginx 1.7.11.3 Gryphon (Included Nginx)
- SQLite (Embedded Database)
- High Speed Internet Min. 10 Mbps with upload speed Min. 5 Mbps or High.
- Processor Min. Dual Core with speed clock 2.40 GHz or High.

## Login Account (Default)
- Username: admin
- Password: 12345678

## Limitation
- Due nginx service built for windows version and some feature may not work like in linux version.
- For now regenerate nginx config file not process automatically, so you need click "Regen Config Stream [Manual]".
- Status RTMP in navbar reseting to beginning if nginx reload because of nginx process use old process and not automatically use new process.
- RTMPS protocol for now not supported on this application, and will supported soon on new version.
- Bug on live video preview (VideoJS) if live streaming is ended sometime the video cannot stop and still running to get live segment.

## How to install
- Extract the ZIP file into a safe folder so it doesn't get mixed up.
- In folder "web_http" change the file ".env.example" to ".env" and change the settings to be as in the .env settings section (APP_KEY is done using artisan)
- First please run the following command " composer install " to install the required dependency libraries.
- Second, please run the command " php artisan key:generate " to generate APP_KEY automatically.
- Thirdly, please run the command " php artisan db:seed " to create a default application account or you can create account from setup page when application first run.
- Fourth, run the web server application and nginx using "startserver.bat" contained in the folder.
- Fifth, open the web page control panel to relay live streaming at " http://localhost:7733 ".

## .env Setting File
```
APP_NAME="RockStream"
APP_ENV=local
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# This database are embedded using sqlite connection
# so you don't need configure this section

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=
# DB_USERNAME=root
# DB_PASSWORD=

# For Nginx system port and path

NGINX_XML_RTMP_PORT=7734
NGINX_HLS_RTMP_PORT=7735
NGINX_PATH="bin/nginx"

BROADCAST_DRIVER=log
CACHE_DRIVER=database
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=database
SESSION_LIFETIME=120

# MEMCACHED_HOST=127.0.0.1

# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# MAIL_MAILER=smtp
# MAIL_HOST=mailhog
# MAIL_PORT=1025
# MAIL_USERNAME=null
# MAIL_PASSWORD=null
# MAIL_ENCRYPTION=null
# MAIL_FROM_ADDRESS=null
# MAIL_FROM_NAME="${APP_NAME}"

# AWS_ACCESS_KEY_ID=
# AWS_SECRET_ACCESS_KEY=
# AWS_DEFAULT_REGION=us-east-1
# AWS_BUCKET=
# AWS_USE_PATH_STYLE_ENDPOINT=false

# PUSHER_APP_ID=
# PUSHER_APP_KEY=
# PUSHER_APP_SECRET=
# PUSHER_APP_CLUSTER=mt1

# MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
# MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

## Screenshot / Demo
![Login Page](https://user-images.githubusercontent.com/30236529/152209280-ccc16465-60f1-4e62-80f5-7a6d535856d5.jpeg)
![Dashboard Page](https://user-images.githubusercontent.com/30236529/152209323-cff58877-4385-49ad-a084-fd440f520c28.jpeg)
![Edit Input & Destination Platform Ingest](https://user-images.githubusercontent.com/30236529/152209320-0cb73d50-8606-4b09-afe4-5d2f4ae3b60c.jpeg)
![Input Stream Page](https://user-images.githubusercontent.com/30236529/152209328-45b99455-a310-4120-821a-f343dca31bb9.jpeg)


## Change Log
- No Log
