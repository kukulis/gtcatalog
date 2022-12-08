#!/bin/bash
set -e

. ./bin/commands/settings.sh

php bin/console --env=$SYMFONY_ENV catalog:process_pictures_import_jobs
