@ECHO OFF
echo stop nginx
taskkill /f /IM nginx.exe >> nul
taskkill /f /IM nginx.exe >> nul
taskkill /f /IM nginx.exe >> nul
taskkill /f /IM nginx.exe >> nul
echo stop php-cgi
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul
taskkill /f /IM php-cgi-spawner.exe >> nul
EXIT