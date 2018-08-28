#!/bin/sh

composer install

sed -i '' 's/Version:           1.0.0/Version:           $TRAVIS_TAG/g' onepay.php
sed -i '' "s/define( 'PLUGIN_NAME_VERSION', '1.0.0' );/define( 'PLUGIN_NAME_VERSION', '$TRAVIS_TAG' );/g" onepay.php

zip -r9 "onepay-$TRAVIS_TAG.zip" . -x Dockerfile composer.json composer.lock docker-compose.yml init.sh package-lock.json docs *.git/\* .DS_Store* .editorconfig* .gitignore* .vscode/\* package.sh README.md
