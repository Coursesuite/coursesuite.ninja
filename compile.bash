#!/bin/bash
TS=$(date +"%Y%m%d%H%M%S")

echo -n "Compiling APIDOC ..."
apidoc -i application/ -o public/apidoc/

cd public/css

rm admin.*.css
rm compiled.*.css

echo -n "CSS: compiled.$TS.css ... "
lessc -ru coursesuite.less compiled.$TS.css
echo ""
lessc -ru admin.less admin.$TS.css

echo ""

cd ../js

rm admin.201*.js
rm main.201*.js

echo -n "JS: admin.$TS.js ... "
uglifyjs --keep-fnames admin.js --output admin.$TS.js
echo ""

echo -n "JS: main.$TS.js ... "
uglifyjs --keep-fnames main.js --output main.$TS.js
echo ""

cd ../../application/core

echo "Updating application constants"
sed -i '' -E "s/compiled.*.css/compiled.$TS.css/g" Application.php
sed -i '' -E "s/admin.*.css/admin.$TS.css/g" Application.php
sed -i '' -E "s/main.*.js/main.$TS.js/g" Application.php
sed -i '' -E "s/admin.*.js/admin.$TS.js/g" Application.php

cd ../..
