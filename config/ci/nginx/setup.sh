#!/bin/bash

set -e
set -x

DIR=$(realpath $(dirname "$0"))
USER=$(whoami)
PHP_VERSION=$(phpenv version-name)
ROOT=$(realpath "$DIR/../../..")
PORT=9000
SERVER="/tmp/php.sock"

function tpl {
    sed \
        -e "s|{DIR}|$DIR|g" \
        -e "s|{USER}|$USER|g" \
        -e "s|{PHP_VERSION}|$PHP_VERSION|g" \
        -e "s|{ROOT}|$ROOT|g" \
        -e "s|{PORT}|$PORT|g" \
        -e "s|{SERVER}|$SERVER|g" \
        < $1 > $2
}

# Make some working directories.
mkdir -p "$DIR/tmp/nginx/sites-enabled"
mkdir -p "$DIR/tmp/var"

# Configure the PHP handler.
PHP_FPM_BIN="$HOME/.phpenv/versions/$PHP_VERSION/sbin/php-fpm"
PHP_FPM_CONF="$DIR/tmp/nginx/php-fpm.conf"

# Build the php-fpm.conf.
tpl "$DIR/nginx/php-fpm.tpl.conf" "$PHP_FPM_CONF"

# Start php-fpm
"$PHP_FPM_BIN" --fpm-config "$PHP_FPM_CONF"

# Build the default nginx config files.
tpl "$DIR/nginx/nginx.tpl.conf" "$DIR/tmp/nginx/nginx.conf"
tpl "$DIR/nginx/fastcgi.tpl.conf" "$DIR/tmp/nginx/fastcgi.conf"
tpl "$DIR/nginx/default-site.tpl.conf" "$DIR/tmp/nginx/sites-enabled/default-site.conf"

# Start nginx.
nginx -c "$DIR/tmp/nginx/nginx.conf"
