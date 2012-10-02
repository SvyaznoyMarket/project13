#!/bin/bash

cd ./dark/data

for file in \
  javascript/bootstrap-transition.js \
  javascript/bootstrap-collapse.js
do
  echo "// $file"
  cat "$file"
  echo
  echo
done > ../web/js/app-common.js

for file in \
  javascript/app-main.js
do
  cp $file ../web/js/
done