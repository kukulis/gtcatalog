#!/bin/bash
set -e

echo -e "\n#$(date +"%Y-%m-%d %H:%M:%S") Atnaujinamas katalogas \n"
git pull

echo -e "\n#$(date +"%Y-%m-%d %H:%M:%S") Išvalomas podėlis \n"
php bin/console cache:clear

echo -e "\n# $(date +"%Y-%m-%d %H:%M:%S") Atnaujimas cron \n"
crontab ../cron/prod.conf

echo -e "\n#$(date +"%Y-%m-%d %H:%M:%S") Laiminga pabaiga \n"