#!/bin/bash
docker-compose stop 
docker-compose rm -vf php-tw-bbs
docker-compose build
docker-compose up -d
