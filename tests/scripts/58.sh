#!/bin/bash

set -o verbose

cd ../../
cp composer.json original-composer.json
cp phpunit-dist.xml phpunit-dist-58.xml
mkdir -p ./build/logs

composer require "orchestra/testbench:3.8.*" --no-update --dev
composer require "orchestra/parser:3.8.*" --no-update
composer require "laravel/framework:5.8.*" --no-update
composer require "phpunit/phpunit:8.*" --no-update --dev

rm composer.json
mv original-composer.json composer.json


php vendor/phpunit/phpunit/phpunit --configuration phpunit-dist-58.xml --coverage-clover ./build/logs/clover.xml
rm phpunit-dist-58.xml
