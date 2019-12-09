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
cp ./readme.txt output

mkdir aviation-weather-from-noaa
cp -R output/* aviation-weather-from-noaa

zip -r aviation-weather-from-noaa.zip aviation-weather-from-noaa
mv ./aviation-weather-from-noaa.zip output

rm -rf ./aviation-weather-from-noaa
