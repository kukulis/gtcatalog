#!/bin/bash
set -e

. ./bin/commands/settings.sh

php bin/console catalog:process_pictures_import_jobs
