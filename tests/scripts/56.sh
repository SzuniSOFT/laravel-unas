#!/bin/bash

cd ../../
cp composer.json original-composer.json
cp phpunit-dist.xml phpunit-dist-56.xml
mkdir -p ./build/logs

composer require "orchestra/testbench:3.6.*" --no-update --dev
composer require "orchestra/parser:3.6.*" --no-update
composer require "laravel/framework:5.6.*" --no-update
composer require "phpunit/phpunit:6.*" --no-update --dev

rm composer.json
mv original-composer.json composer.json


php vendor/phpunit/phpunit/phpunit --configuration phpunit-dist-56.xml --coverage-clover ./build/logs/clover.xml
rm phpunit-dist-56.xml
