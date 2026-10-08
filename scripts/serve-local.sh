#!/bin/sh
set -eu
cd "$(dirname "$0")/../public"
exec php -d upload_max_filesize=10M -d post_max_size=128M -d memory_limit=256M -S 127.0.0.1:8013 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
