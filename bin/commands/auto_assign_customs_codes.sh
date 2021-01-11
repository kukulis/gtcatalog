#!/bin/bash
set -e

. ./bin/commands/settings.sh

php bin/console catalog:auto_assign_customs_numbers
