#!/bin/bash
# stop on errors
set -e

MAINTAINANCE_MODE_FILE="./var/locks/MAINTAINANCE_MODE"
PID_FILE="./var/locks/$1"
LOG_FILE="./var/log/$1.log"
SCRIPT_FILE="./bin/commands/$1.sh"

touch "$LOG_FILE"

if [ -e "$MAINTAINANCE_MODE_FILE" ]; then
    echo -e "MAINTAINANCE_MODE. praleistas $SCRIPT_FILE \n" >> "$LOG_FILE"
    ( >&2 echo -e "MAINTAINANCE_MODE. praleistas $SCRIPT_FILE \n")
    exit 1
fi

if [ -e "$PID_FILE" ]; then
    pid=`cat "$PID_FILE"`
    if kill -0 &>1 > /dev/null $pid; then
        # Jau veikia

        ( >&2 echo -e "Dar paleistas $SCRIPT_FILE \n")

        ( >&2 tail "$LOG_FILE" )

        exit 1
    else
        rm "$PID_FILE"
    fi
fi

NAUJAS_PID=$$
echo $NAUJAS_PID > "$PID_FILE"

bash "$SCRIPT_FILE" "$2" "$3" "$4" "$5" >> "$LOG_FILE"

exit 0
