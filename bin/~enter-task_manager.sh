#!/bin/bash

while true; do
cd /opt/WWWRoot/green.testground.ru/wwwroot && php symfony task-manager:run >> /dev/null
sleep 20
done
