#!/bin/bash
# Bash script to run Docker in single command on Mac OS X.
# Without any parameters it only starts VM and runs the web
#
# Parameters:
#  -b    proceed with build
#

DINGHY_STATUS=$(dinghy status);

if [[ $DINGHY_STATUS == *"stopped"* ]]
then
    echo "Starting dinghy..."
    DINGHY_CMD="dinghy up | grep 'export DOCKER_'"
else
    echo "Dinghy is already running."
    DINGHY_CMD="dinghy shellinit | grep 'export DOCKER'"
fi

IFS=$'\n'
for COMMAND in `eval $DINGHY_CMD`
do
    echo "Setting environment variable $COMMAND ..."
    eval $COMMAND
done
unset IFS

echo "Environment variables were set."

IFS=$'\n'
for PROXY_ID in $(docker ps | grep "codekitchen/dinghy-http-proxy" | cut -d ' ' -f 1)
do
    echo "Killing http proxy ($PROXY_ID)..."
    eval "docker kill $PROXY_ID"
    echo "Http proxy should be killed."
done
unset IFS

if [ "$1" = "-b" ]
    then
        echo "Building web..."
        docker-compose build
fi

echo "Starting web..."
docker-compose up
