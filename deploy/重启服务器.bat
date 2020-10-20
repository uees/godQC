@ECHO OFF
ECHO Stop Nginx Http Server
taskkill /f /IM nginx.exe >> nul
taskkill /f /IM nginx.exe >> nul
taskkill /f /IM nginx.exe >> nul
taskkill /f /IM nginx.exe >> nul

ECHO Stop PHP Runtime
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul

SET PHP_FCGI_MAX_REQUESTS=2000
SET PHP_HELP_MAX_REQUESTS=100

ECHO Starting PHP Runtime...
rem RunHiddenConsole D:\opt\php\php-cgi.exe  -b 127.0.0.1:9999 -c D:\opt\php\php.ini
RunHiddenConsole php-cgi-spawner.exe "D:/opt/php/php-cgi.exe -c D:/opt/php/php.ini" 9999 4+16

ECHO Starting Nginx Http Server...
RunHiddenConsole D:\opt\nginx\nginx.exe -p D:\opt\nginx

ping 127.0.0.1 -n 1>NUL
ECHO .
ECHO .
ECHO .
ping 127.0.0.1 >NUL
