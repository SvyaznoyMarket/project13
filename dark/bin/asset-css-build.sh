#!/bin/bash

cd ./dark/data

lessc less/bootstrap.less ../web/css/main.css
lessc less/app-product.less ../web/css/product.css