#!/bin/bash

rm -rf ./output
mkdir ./output
cp -R ./admin output
cp -R ./build output
cp -R ./classes output
cp -R ./css output
cp -R ./js output
cp -R ./lang output
cp -R ./vendor output
cp -R ./views output
cp ./aviation-weather-from-noaa.php output
cp ./readme.md output
cp ./readme.txt output

mkdir awfn-tmp
cp -R output/* awfn-tmp

zip -r aviation-weather-from-noaa.zip awfn-tmp
mv ./aviation-weather-from-noaa.zip output

rm -rf ./awfn-tmp
