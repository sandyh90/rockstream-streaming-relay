@ECHO OFF

TITLE RockStream Server Control

:PROMPTACTION
ECHO ============================
ECHO Welcome to RockStream Server Control
ECHO ============================
SET /p action_server="Please choose action to server? [START,STOP,CANCEL] : "
IF /i "%action_server%" EQU "START" GOTO STARTSERVER
IF /i "%action_server%" EQU "STOP" GOTO STOPSERVER
IF /i "%action_server%" EQU "CANCEL" GOTO ENDPROMPT
ECHO That option you choose is not valid. Please try again.
GOTO PROMPTACTION

:STARTSERVER
ECHO Starting server...
QPROCESS * | find /I /N "php.exe">NUL
IF "%ERRORLEVEL%"=="0" (
	ECHO PHP already running
)else (
    ECHO Starting PHP
    cmd /c start "" "%cd%\bin\RunHiddenConsole.exe" "%cd%\bin\php\php.exe" -S 127.0.0.1:7733 -t "web_http/public"
)

QPROCESS * | find /I /N "nginx.exe">NUL
IF "%ERRORLEVEL%"=="0" (
	ECHO NGINX already running
)else (
    ECHO Starting NGINX
    cmd /c start "" /d"%cd%\bin\nginx" "nginx.exe"
)

TIMEOUT 2 > NUL
CLS

SET /p action_openweb="Are you want to open control panel? [Y,N] :"

IF /i "%action_openweb%" EQU "Y" GOTO OPENWEBCONTROL
IF /i "%action_openweb%" EQU "N" GOTO NOTIFYOPENWEB
ECHO That option you choose is not valid. Please try again.
GOTO PROMPTACTION

:OPENWEBCONTROL
start "" http://localhost:7733/
GOTO PROMPTACTION

:NOTIFYOPENWEB
ECHO Open web control panel at http://localhost:7733/ to control server process.
GOTO PROMPTACTION

:STOPSERVER
ECHO Stopping server...
QPROCESS * | find /I /N "nginx.exe">NUL
IF "%ERRORLEVEL%"=="0" (
    taskkill /f /IM nginx.exe
    ECHO NGINX ended successfully.
)else (
	ECHO NGINX is not running
)

QPROCESS * | find /I /N "php.exe">NUL
IF "%ERRORLEVEL%"=="0" (
    taskkill /f /IM php.exe
    ECHO PHP ended successfully.
)else (
	ECHO PHP is not running
)

TIMEOUT 2 > NUL
CLS

GOTO PROMPTACTION

:ENDPROMPT
ECHO Exiting server control...
TIMEOUT 2 > NUL
CLS
EXIT /b