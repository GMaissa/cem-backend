#!/bin/sh

# Need this to fix an issue with sudoers config
#chown -R 0:0 /etc/sudoers.d

if [ ! -z ${XDEBUG_REMOTE_HOST} ]; then
    . $(dirname $0)/configure-xdebug.sh
fi

if [ ! -z ${BLACKFIRE_ENABLE} ]; then
    . $(dirname $0)/configure-blackfire.sh
fi

exec "$@"
