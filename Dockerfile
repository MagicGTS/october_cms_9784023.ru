ARG BUILD_LICENSE_KEY=demo
ARG CI_REGISTRY=localhost
FROM ${CI_REGISTRY}/lnmp:latest

LABEL org.opencontainers.image.title="Laravel LDAP user manager" \
    org.opencontainers.image.authors="Andrey Leshkevich <magicgts@gmail.com>" \
    org.opencontainers.image.description="Web appliance for manage LDAP users" \
    org.opencontainers.image.version="0.8"
RUN set -eux && \
    touch /var/log/php-fpm.log && \
    chown --quiet -R nginx:root /etc/nginx/ /etc/php-fpm.d/ /etc/php-fpm.conf /var/log/php-fpm /var/log/php-fpm.log /var/log/nginx && \
    chgrp -R 0 /etc/nginx/ /etc/php-fpm.d/ /etc/php-fpm.conf && \
    chmod -R g=u /etc/nginx/ /etc/php-fpm.d/ /etc/php-fpm.conf && \
    mkdir -p /var/lib/php/{session,wsdlcache} && \    
    mkdir -p /var/lib/php/session /opt/framework /var/cache/nginx && \
    composer create-project october/october /opt/framework && \
    cd /opt/framework && \
    composer require barryvdh/laravel-debugbar --dev && \
    php artisan project:set ${BUILD_LICENSE_KEY} && \
    php artisan october:build && \
    sed -i "s/logfile \/var\/log\/redis\/redis\.log/logfile \/dev\/stdout/g" /etc/redis.conf && \
    touch /opt/framework/.firstrun && \
    chown --quiet -R nginx:root /var/lib/php/{session,wsdlcache}/ /opt/framework /opt/framework/.firstrun /var/cache/nginx /var/lib/redis && \
    rm -rf /var/lib/mysql/* && \
    chgrp -R 0 /var/lib/php/{session,wsdlcache}/ && \
    chmod -R g=u /var/lib/php/{session,wsdlcache}/ && \
    rm -f /etc/nginx/conf.d/php-fpm.conf /etc/nginx/default.d/php.conf /etc/nginx/nginx.conf /etc/php-fpm.d/www.conf /opt/framework/auth.json && \
    rm -f /etc/opt/remi/php81/php-fpm.d/www.conf /etc/opt/remi/php81/{php.ini,php-fpm.conf} /etc/opt/remi/php81/php.d/15-xdebug.ini && \
    rm -f /etc/php-fpm.conf && \
    rm -rf /var/lib/mysql/* && \
    chown --quiet -R nginx. /var/lib/mysql

COPY --chown=1000:0 ["conf/etc/", "/etc/"]

COPY --chown=1000:0 ["framework/", "/opt/framework/"]

COPY --chown=1000:0 ["db/october.sql", "/var/lib/mysql/"]

EXPOSE 8080/TCP
EXPOSE 3306/TCP
EXPOSE 3000/TCP
EXPOSE 3001/TCP

ENV CLIENT_HOST=localhost
ENV CONTAINER_PROTO=http
ENV APP_LOCALE=en
ENV DB_DATABASE=october
ENV DB_USERNAME=october
ENV DB_PASSWORD=12345
ENV DB_HOST=127.0.0.1
ENV LICENSE_KEY=demo
ENV ACTIVE_THEME=demo
ENV BACKEND_URI='\/backend'

WORKDIR /opt/framework

COPY --chmod=755 ["docker-entrypoint.sh", "/usr/bin/"]

USER 1000

VOLUME ["/opt/framework", "/var/lib/mysql"]

ENTRYPOINT ["docker-entrypoint.sh"]