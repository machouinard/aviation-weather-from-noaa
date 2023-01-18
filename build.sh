#!/bin/bash

rm -rf ./dist
mkdir ./dist
cp -R ./admin dist
cp -R ./build dist
cp -R ./classes dist
cp -R ./css dist
cp -R ./js dist
cp -R ./lang dist
cp -R ./vendor dist
cp -R ./views dist
cp ./aviation-weather-from-noaa.php dist
cp ./readme.txt dist

mkdir aviation-weather-from-noaa
cp -R dist/* aviation-weather-from-noaa

zip -r aviation-weather-from-noaa.zip aviation-weather-from-noaa
mv ./aviation-weather-from-noaa.zip dist

rm -rf ./aviation-weather-from-noaa
