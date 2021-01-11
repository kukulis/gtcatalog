#!/bin/bash
set -e

. settings.sh
php bin/console catalog:auto_assign_customs_numbers
