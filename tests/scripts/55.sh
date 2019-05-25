#!/bin/bash

set -o verbose

cp composer.json original-composer.json
cp phpunit-dist.xml phpunit-dist-55.xml
mkdir -p ./build/logs

composer require "orchestra/testbench:3.5.*" --no-update --dev
composer require "orchestra/parser:3.5.*" --no-update
composer require "laravel/framework:5.5.*" --no-update
composer require "phpunit/phpunit:6.*" --no-update --dev

rm composer.json
mv original-composer.json composer.json


php vendor/phpunit/phpunit/phpunit --configuration phpunit-dist-55.xml --coverage-clover ./build/logs/clover.xml
rm phpunit-dist-55.xml
