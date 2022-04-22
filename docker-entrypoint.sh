#!/bin/bash

set -o pipefail

set +e

# Script trace mode
if [ "${DEBUG_MODE,,}" == "true" ]; then
    set -o xtrace
fi

# Default timezone for web interface
: ${PHP_TZ:="Europe/Moscow"}

# Default directories
# Web interface www-root directory
WWW_ROOT="/opt/framework"

sed -i "s/^\s*;\?\s*xdebug\.client_host\s*=.*/xdebug.client_host = ${CLIENT_HOST}/g" /etc/opt/remi/php81/php.d/15-xdebug.ini
sed -i "s/^\s*mix\s*\.\s*browserSync.*$/mix.browserSync({ host: '${CONTAINER_HOST}', proxy: '${CLIENT_HOST}', port: 3000, open: false, });/g" /opt/framework/webpack.mix.js

if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "######################DB################################"
    /usr/bin/mysql_install_db --auth-root-authentication-method=normal
    /usr/bin/mysqld_safe --socket /tmp/mysql &
    sleep 5
    /usr/bin/mysql -u root --binary-mode --protocol=SOCKET --socket=/tmp/mysql mysql <<<"SET @@SESSION.SQL_LOG_BIN=0;SET @@SESSION.SQL_MODE=REPLACE(@@SESSION.SQL_MODE, 'NO_BACKSLASH_ESCAPES', '');DELETE FROM mysql.user WHERE user NOT IN ('mysql.sys', 'mariadb.sys', 'mysqlxsys', 'root') OR host NOT IN ('localhost') ;DELETE FROM mysql.db WHERE Db='test' OR Db='test\_%' ; DROP DATABASE IF EXISTS test ; GRANT ALL ON *.* TO 'root'@'localhost' WITH GRANT OPTION ;FLUSH PRIVILEGES ;"
    /usr/bin/mysql -u root --binary-mode --protocol=SOCKET --socket=/tmp/mysql </var/lib/mysql/october.sql
    /usr/bin/mysql -u root --binary-mode --protocol=SOCKET --socket=/tmp/mysql mysql <<<"CREATE USER 'october'@'127.0.0.1' IDENTIFIED BY '12345'; GRANT USAGE ON *.* TO 'october'@'127.0.0.1' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0; GRANT ALL PRIVILEGES ON october.* TO 'october'@'127.0.0.1' WITH GRANT OPTION;"
    /usr/bin/mysqladmin -u root --protocol=SOCKET --socket=/tmp/mysql shutdown

fi

if [ -f "/opt/framework/.firstrun" ]; then
    echo "######################init################################"
    /usr/bin/mysql_install_db --auth-root-authentication-method=normal
    /usr/bin/mysqld_safe --socket /tmp/mysql &
    composer dump-autoload
    sed -i "s/^\s*APP_URL\s*=.*$/APP_URL=${CONTAINER_PROTO}:\/\/${CONTAINER_HOST}/g;\
    s/^\s*APP_LOCALE\s*=.*$/APP_LOCALE=${APP_LOCALE}/g;\
    s/^\s*DB_DATABASE\s*=.*$/DB_DATABASE=${DB_DATABASE}/g;\
    s/^\s*DB_USERNAME\s*=.*$/DB_USERNAME=${DB_USERNAME}/g;\
    s/^\s*DB_PASSWORD\s*=.*$/DB_PASSWORD=${DB_PASSWORD}/g;\
    s/^\s*DB_HOST\s*=.*$/DB_HOST=${DB_HOST}/g;\
    s/^\s*ACTIVE_THEME\s*=.*$/ACTIVE_THEME=${ACTIVE_THEME}/g;\
    s/^\s*BACKEND_URI\s*=.*$/BACKEND_URI=${BACKEND_URI}/g" /opt/framework/.env
    php artisan project:set ${LICENSE_KEY}
    php artisan october:migrate
    php artisan october:mirror
    #npm run dev
    /usr/bin/mysqladmin -u root --protocol=SOCKET --socket=/tmp/mysql shutdown
    rm -f /opt/framework/.firstrun
fi

echo "########################################################"

if [ "$1" != "" ]; then
    echo "** Executing '$@'"
    exec "$@"
elif [ -f "/usr/bin/supervisord" ]; then
    echo "** Executing supervisord"
    exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
else
    echo "Unknown instructions. Exiting..."
    exit 1
fi

#################################################
