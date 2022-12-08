#!/bin/bash
set -e

. ./bin/commands/settings.sh

php bin/console --env=$SYMFONY_ENV catalog:auto_assign_customs_numbers_by_keywords
