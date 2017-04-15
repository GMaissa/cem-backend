#!/bin/sh

STEP='Configuring XDebug'
echo "[....] "${STEP}

if [ $(php -m | grep 'xdebug' | wc -l) -eq 0 ]; then
    apt-get install -y --allow-unauthenticated php-xdebug
fi

XDEBUG_CONFIG_FILE=$(php -i | grep xdebug | grep ini | cut -d ',' -f 1)

if [ -f "${XDEBUG_CONFIG_FILE}" ]; then
    if [ $(cat "${XDEBUG_CONFIG_FILE}" | grep 'xdebug.remote_enable' | wc -l) -eq 0 ]; then
        echo "xdebug.remote_enable=1" >> "${XDEBUG_CONFIG_FILE}"
    fi

    if [ ! -z ${XDEBUG_REMOTE_HOST} ]; then
        if [ $(cat "${XDEBUG_CONFIG_FILE}" | grep 'xdebug.remote_host' | wc -l) -eq 0 ]; then
            echo "xdebug.remote_host=${XDEBUG_REMOTE_HOST}" >> "${XDEBUG_CONFIG_FILE}"
        else
            sed -i "s#xdebug.remote_host=.*#xdebug.remote_host=${XDEBUG_REMOTE_HOST}#g" "${XDEBUG_CONFIG_FILE}"
        fi
    fi

    if [ ! -z ${XDEBUG_IDEKEY} ]; then
        if [ $(cat "${XDEBUG_CONFIG_FILE}" | grep 'xdebug.idekey' | wc -l) -eq 0 ]; then
            echo "xdebug.idekey=${XDEBUG_IDEKEY}" >> "${XDEBUG_CONFIG_FILE}"
        else
            sed -i "s#xdebug.idekey=.*#xdebug.idekey=${XDEBUG_IDEKEY}#g" "${XDEBUG_CONFIG_FILE}"
        fi
    fi

    if [ ! -z ${XDEBUG_REMOTE_PORT} ]; then
        if [ $(cat "${XDEBUG_CONFIG_FILE}" | grep 'xdebug.remote_port' | wc -l) -eq 0 ]; then
            echo "xdebug.remote_port=${XDEBUG_REMOTE_PORT}" >> "${XDEBUG_CONFIG_FILE}"
        else
            sed -i "s#xdebug.remote_port=.*#xdebug.remote_port=${XDEBUG_REMOTE_PORT}#g" "${XDEBUG_CONFIG_FILE}"
        fi
    fi
fi

echo "[ OK ] "${STEP}
