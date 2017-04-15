#!/bin/sh

if [ $(php -m | grep 'blackfire' | wc -l) -eq 0 ]; then
    STEP='Configuring Blackfire probe'
    echo "[....] "${STEP}

    if [ -f ${BLACKFIRE_AGENT_HOST} ];then
        BLACKFIRE_AGENT_HOST='blackfire:8707'
    fi

    version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") && \
    curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version && \
    tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp && \
    mv /tmp/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so && \
    echo "extension=blackfire.so\nblackfire.agent_socket=tcp://${BLACKFIRE_AGENT_HOST}" > /etc/php/7.1/mods-available/blackfire.ini
    ln -s /etc/php/7.1/mods-available/blackfire.ini /etc/php/7.1/fpm/conf.d/20-blackfire.ini
    ln -s /etc/php/7.1/mods-available/blackfire.ini /etc/php/7.1/cli/conf.d/20-blackfire.ini

    echo "[ OK ] "${STEP}
fi
