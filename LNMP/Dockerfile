FROM localhost/rockylinux-updated:latest

LABEL org.opencontainers.image.title="RockyLinux LNMP base (Nginx, PHP-FPM, MySQL)" \
    org.opencontainers.image.authors="Andrey Leshkevich <magicgts@gmail.com>" \
    org.opencontainers.image.description="RockyLinux LNMP base (Nginx, PHP-FPM, MySQL)" \
    org.opencontainers.image.version="0.8"

RUN set -eux && \
    dnf install https://rpms.remirepo.net/enterprise/remi-release-8.rpm -y && \
    curl -LsS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | bash && \
    dnf upgrade -y && \
    dnf update -y && \
    groupadd -g 1000 -r nginx && \
    useradd -u 1000 -r -g nginx -G root -s /sbin/nologin -d /var/cache/nginx -c "nginx user"  nginx && \
    dnf module reset php nodejs && \
    dnf module install php:remi-8.1 nginx:1.20 nodejs:12 -y && \
    REPOLIST="baseos,appstream,epel,mariadb-main,extras,remi-modular,remi-safe" && \
    INSTALL_PKGS="nginx \
    php81-php-bcmath \
    php81-php-cli \
    php81-php-dba \
    php81-php-fpm \
    php81-php-gd \
    php81-php-imap \
    php81-php-intl \
    php81-php-json \
    php81-php-ldap \
    php81-php-mbstring \
    php81-php-mysqlnd \
    php81-php-pdo \
    php81-php-pear \
    php81-php-pecl-apcu \
    php81-php-pecl-mcrypt \
    php81-php-pecl-memcached \
    php81-php-xml \
    php81-php-xmlrpc \
    php81-php-opcache \
    php81-php-pecl-xdebug3 \
	php81-php-phpiredis \
    php81-libzip \
    php81-php-pecl-zip \
    redis \
    composer \
    git \
    php-mysqlnd \
    boost-program-options \
    MariaDB-server \
    MariaDB-client" && \
    dnf -y install \
    --disablerepo "*" \
    --enablerepo "${REPOLIST}" \
    --setopt=tsflags=nodocs \
    --setopt=install_weak_deps=False \
    --best \
    ${INSTALL_PKGS} && \
    dnf -y clean all && \
    rm -rf /var/cache/yum /var/lib/yum/yumdb/* /usr/lib/udev/hwdb.d/* && \
    rm -rf /var/cache/dnf /etc/udev/hwdb.bin /root/.pki && \
    rm -rf /etc/{php.d,php-fpm.d,php.ini,php-fpm.conf} /usr/bin/php* && \
    for i in php.ini php.d php-fpm.conf php-fpm.d; do ln -s /etc/opt/remi/php81/$i /etc/$i; done && \
    for i in php php-cgi phpize; do ln -s /opt/remi/php81/root/bin/$i /usr/bin/$i; done && \
    ln -s /opt/remi/php81/root/bin/php /usr/bin/php81 && \
    ln -s /opt/remi/php81/root/bin/php-cgi /usr/bin/php81-cgi  && \
    ln -s /opt/remi/php81/root/bin/pear /usr/bin/php81-pear  && \
    ln -s /opt/remi/php81/root/bin/phar /usr/bin/php81-phar