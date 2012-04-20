#!/usr/local/bin/bash

# this is just a simple script that calls the rebuild script if any manifests
# below the resource directory changes.

# Create a temporary file name that gives preference
# to the user's local tmp directory and has a name
# that is resistant to "temp race attacks"

if [ -d "~/tmp" ]; then
    TEMP_DIR=~/tmp
else
    TEMP_DIR=/tmp
fi

TEMP_FILE=$TEMP_DIR/.rs-build
PROGNAME=$(basename $0)
SCRIPT_DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
BUILD_EXEC=$SCRIPT_DIR/build-rs-tree.php

function usage {
    # Display usage message on standard error
    echo "Usage: $PROGNAME <dir>" 1>&2
}

function clean_up {
    # Perform program exit housekeeping
    # Optionally accepts an exit status
    rm -f $TEMP_FILE
    exit $1
}

function error_exit {
    # Display error message and exit
    echo "${PROGNAME}: ${1:-"Unknown Error"}" 1>&2
    clean_up 1
}

trap clean_up SIGHUP SIGINT SIGTERM

if [ $# != "1" ]; then
    usage
    error_exit "you must specify the directory you want to watch for changes in."
fi

echo "Watching $1 for changes..."

echo "Build script $BUILD_EXEC"

touch $TEMP_FILE

while true; do
  find $1 -newer $TEMP_FILE \
      -type f \
      -name '*.json' \
      -exec $BUILD_EXEC {} + \
      -exec bash -c 'echo -ne $"Rebuilding...\n"' {} + \
      -exec bash -c 'echo -ne "$@\n"' X {} + \
      -exec bash -c 'echo -ne $"\n** Changes detected:\n"' {} +

  touch $TEMP_FILE
  sleep 2
done

