#!/bin/bash

# This generates a file every 5 minutes

while true; do
#echo 1
cd /opt/WWWRoot/green.testground.ru/wwwroot && php symfony task-manager:run >> log/cron.log
sleep 20
done
