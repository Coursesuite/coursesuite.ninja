#!/bin/bash

less=/usr/local/lib/node_modules/less/bin/lessc
miniJs=/usr/local/lib/node_modules/uglify-es/bin/uglifyjs

TS=$(date +"%Y%m%d%H%M%S")

echo -n "Compiling APIDOC ..."
apidoc -i application/ -o public/apidoc/

cd public/css

rm admin.*.css
rm compiled.*.css

echo -n "CSS: compiled.$TS.css ... "
$less -ru coursesuite.less compiled.$TS.css
echo ""
$less -ru admin.less admin.$TS.css

echo ""

cd ../js

rm admin.201*.js
rm main.201*.js

echo -n "JS: admin.$TS.js ... "
$miniJs --keep-fnames admin.js --output admin.$TS.js
echo ""

echo -n "JS: main.$TS.js ... "
$miniJs --keep-fnames main.js --output main.$TS.js
echo ""

cd ../../application/core

echo "<?php" > Variables.php
echo "DEFINE('APP_CSS', '/css/compiled.$TS.css');" >> Variables.php
echo "DEFINE('APP_JS', '/js/main.$TS.js');" >> Variables.php
echo "DEFINE('ADMIN_CSS', '/css/admin.$TS.css');" >> Variables.php
echo "DEFINE('ADMIN_JS', '/js/admin.$TS.js');" >> Variables.php
echo "?>" >> Variables.php

cd ../..

rm -r -f deploy
mkdir deploy

cp application deploy
cp public deploy
cp precompiled deploy
cp vendor deploy
cp websockets deploy

ls | grep -v 'deploy' | parallel rm