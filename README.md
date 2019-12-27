# Revars
Переменные и заменяемый текст для Joomla!

Переменные для сайта и абсолютно заменяемый текст.
Переменные можно использовать на сайте подобно всем плагинам джумлы таким образом: {VAR_NAME}, где NAME - это имя переменной. Именно это имя нужно указывать таблице. Имена переменных нужно писать большими буквами (в верхнем регистре).

Cистемные переменные, которые можно использовать в своих переменных:
{VAR_SERVER_NAME} - Имя хоста, на котором выполняется текущий скрипт. Например, www.site.ru. Это имя берется из настроек сервера и не зависит от адреса запроса.
{VAR_HTTP_HOST} - Содержимое заголовка Host: из текущего запроса, если он есть. Например, site.ru. Это содержимое берется из запроса браузера и зависит от него. Если кто-то набрал www.site.ru вместо site.ru - то вы получите именно www.site.ru
{VAR_REQUEST_URI} - URI, который был предоставлен для доступа к этой странице. Например, '/index.html'.
{VAR_REMOTE_ADDR} - IP-адрес, с которого пользователь просматривает текущую страницу.
