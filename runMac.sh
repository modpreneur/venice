#!/bin/bash
# ME Docker script.
# For help, run it with --help parameter

# CONFIGURATION #
#################
DB_NAME="venice"
DB_DRIVER="mysql" # mysql | pgsql
DB_USER="user"
DB_PASS="pass"

PATH_CONSOLE="bin/console"
PATH_CACHE="/var/app/var/cache"
PATH_LOGS="/var/app/var/logs"


# CODE #
########

VERSION="1.1.1"

RED='\033[0;31m'
ORANGE='\033[0;33m'
GREEN='\033[0;32m'
NC='\033[0m'

OS=$(uname)

if [[ ! "$OS" = "Linux" && ! "$OS" = "Darwin" ]]
    then
        printf "${RED}Unsupported OS${NC}\n"
        exit 0
fi


function showHelp {
    echo "
Usage: run [ option ]

ME Docker script

Options:

    -b                          Run with build
    -s                          Connect to Web container shell"

if [[ $DB_DRIVER = "mysql" ]]
    then
        echo "    -m                          Connect to MySQL (type '\q' to exit)"
elif [[ $DB_DRIVER = "pgsql" ]]
    then
        echo "    -p                          Connect to PostgreSQL (type '\q' to exit)"
fi
    echo "    -c  [ install | update ]    Run Composer
    -sf [ command ]               Call command to Symphony console

    --create-database           Create database
    --drop-database             Drop database
    --migrate-database          Migrate database
    --recreate-database         Drop, create and migrate database
    --cache-logs-clear          Clear cache and logs

    --install                   Install Docker (Mac OS only)

    --version                   Show script version information

If no parameters are present, web will be only run.
    "
}

function showVersion {
    echo "ME Docker script version $VERSION"
}

function prepareDinghy {
    if [[ "$OS" = "Darwin" ]]
        then
            DINGHY_STATUS=$(dinghy status 2>&1);

            if [[ $DINGHY_STATUS == *"stopped"* || $DINGHY_STATUS == *"Stopped"* ]]
            then
                echo "Starting dinghy..."
                DINGHY_CMD="dinghy up"
            else
                echo "Dinghy is already running."
                DINGHY_CMD="dinghy shellinit"
            fi

            echo "Setting environment variables..."

            IFS=$'\n'
            for COMMAND in `eval $DINGHY_CMD " | grep 'export DOCKER_'"`
            do
#                echo "Setting environment variable $COMMAND ..."
                eval $COMMAND
            done
            unset IFS

            echo "Environment variables were set."
    fi
}

function killDinghyHttpProxy {
    IFS=$'\n'
    for PROXY_ID in $(docker ps | grep "codekitchen/dinghy-http-proxy" | cut -d ' ' -f 1)
    do
        echo "Killing http proxy ($PROXY_ID)..."
        eval "docker kill $PROXY_ID"
        echo "Http proxy should be killed."
    done
    unset IFS
}

function buildWeb {
    echo "Building web..."
    docker-compose build
}

function runWeb {
    echo "Starting web..."
    docker-compose up
}

# Param 1: Container
# Param 2: Command
function runCommand {
    VM_CONNECTED=false

    IFS=$'\n'
    for CONT_ID in $(docker ps 2> /dev/null | grep "$1" | cut -d ' ' -f 1)
    do
        echo "Connecting to container ($CONT_ID)..."
        eval "docker exec -it $CONT_ID ${2-bash}"
        VM_CONNECTED=true
    done
    unset IFS

    if [[ "$VM_CONNECTED" = false ]]
        then
            printf "${RED}ERROR: Container is not running!${NC}\n"
            exit 0
    fi
}

#Param 1: command
function runSymfonyConsole {
    prepareDinghy
    runCommand "_web" "bash -c 'php $PATH_CONSOLE $1'"
}

#Param 1: Command
function runSqlCommand {
    prepareDinghy
    if [[ $DB_DRIVER = "pgsql" ]]
        then
            runCommand "postgres" "su postgres -c \"psql -U postgres -c '$1;'\""
    elif [[ $DB_DRIVER = "mysql" ]]
        then
            runCommand "mysql" "bash -c \"mysql -u $DB_USER -p$DB_PASS -e '$1;'\""
    else
        printf "${RED}ERROR: Invalid database driver specified in configuration!${NC}\n"
        exit 0
    fi

}

#Param 1: Database name
function createDatabase {
    runSqlCommand "CREATE DATABASE $DB_NAME"
}

#Param 1: Database name
function dropDatabase {
    runSqlCommand "DROP DATABASE $DB_NAME"
}

#Param 1: Database name
function recreateDatabase {
    runSqlCommand "DROP DATABASE $DB_NAME; CREATE DATABASE $DB_NAME"
}

#Param 1: command
function runComposer {
    prepareDinghy
    runCommand "_web" "bash -c 'composer $1'"
}

function migrateDatabase {
    prepareDinghy
    runCommand "_web" "bash -c 'php $PATH_CONSOLE doctrine:migrations:migrate --no-interaction'"
}

function clearCacheAndLogs {
    prepareDinghy
    runCommand "_web" "bash -c 'rm -r $PATH_CACHE/dev/; rm -r $PATH_CACHE/prod; rm $PATH_LOGS/dev.log; rm $PATH_LOGS/test.log'"
}

function install {
    echo "### Docker installation ###"
    if [[ "$OS" = "Darwin" ]]
        then
            installMac
    else
        printf "${RED}Unsupported OS${NC}\n"
    fi
}

function installMac {
    TOTAL="12"

    printf "${ORANGE}[ 1/$TOTAL] Downloading Virtualbox...${NC}\n"
    curl "http://download.virtualbox.org/virtualbox/5.0.10/VirtualBox-5.0.10-104061-OSX.dmg" -o "VirtualBox-5.0.10-104061-OSX.dmg"

    printf "${ORANGE}[ 2/$TOTAL] Mounting volume...${NC}\n"
    hdiutil mount VirtualBox-5.0.10-104061-OSX.dmg

    printf "${ORANGE}[ 3/$TOTAL] Installing Virtualbox...${NC}\n"
    sudo installer -pkg /Volumes/VirtualBox/VirtualBox.pkg -target /

    printf "${ORANGE}[ 4/$TOTAL] Unmouting volume...${NC}\n"
    hdiutil unmount /Volumes/VirtualBox/

    printf "${ORANGE}[ 5/$TOTAL] Removing Virtualbox installation package...${NC}\n"
    rm VirtualBox-5.0.10-104061-OSX.dmg

    printf "${ORANGE}[ 6/$TOTAL] Installing brew...${NC}\n"
    ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"

    printf "${ORANGE}[ 7/$TOTAL] Installing dinghy...${NC}\n"
    brew install https://github.com/codekitchen/dinghy/raw/v4.0.7/dinghy.rb

    printf "${ORANGE}[ 8/$TOTAL] Downloading Docker Toolbox...${NC}\n"
    curl -L "https://github.com/docker/toolbox/releases/download/v1.9.1c/DockerToolbox-1.9.1c.pkg" -o "DockerToolbox-1.9.1c.pkg"

    printf "${ORANGE}[ 9/$TOTAL] Installing Docker Toolbox...${NC}\n"
    sudo installer -pkg ./DockerToolbox-1.9.1c.pkg -target /

    printf "${ORANGE}[10/$TOTAL] Removing Docker Toolbox installation package...${NC}\n"
    rm DockerToolbox-1.9.1c.pkg

    printf "${ORANGE}[11/$TOTAL] Creating dinghy virtual machine...${NC}\n"
    dinghy create

    printf "${ORANGE}[12/$TOTAL] Stopping dinghy virtual machine...${NC}\n"
    dinghy stop

    printf "${ORANGE}[13/$TOTAL] Configuring dinghy virtual machine...${NC}\n"
    VboxManage modifyvm "dinghy" --cpus 2

    printf "${GREEN}Installation is complete.${NC}\n"
}

function installLinux {
    TOTAL="9"

    printf "${ORANGE}[ 1/$TOTAL] Installing required base packages...${NC}\n"
    apt-get install apt-transport-https git linux-image-extra-$(uname -r)
    sudo apt-get install apt-transport-https git linux-image-extra-$(uname -r)


    printf "${ORANGE}[ 2/$TOTAL] Purging old Docker packages...${NC}\n"
    apt-get purge lxc-docker*
    apt-get purge docker.io*
    sudo apt-get purge lxc-docker*
    sudo apt-get purge docker.io*

    printf "${ORANGE}[ 3/$TOTAL] Adding key to apt...${NC}\n"
    apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D
    sudo apt-key adv --keyserver hkp://p80.pool.sks-keyservers.net:80 --recv-keys 58118E89F3A912897C070ADBF76221572C52609D

    printf "${ORANGE}[ 4/$TOTAL] Adding docker to apt sources...${NC}\n"
#    deb https://apt.dockerproject.org/repo debian-jessie main
#    deb https://apt.dockerproject.org/repo ubuntu-trusty main
#
#    > /etc/apt/sources.list.d/docker.list

    printf "${ORANGE}[ 5/$TOTAL] Updating packages database...${NC}\n"
    apt-get update
    sudo apt-get update

    printf "${ORANGE}[ 6/$TOTAL] Installing Docker engine...${NC}\n"
    apt-get install docker-engine
    sudo apt-get install docker-engine

    printf "${ORANGE}[ 7/$TOTAL] Starting Docker service...${NC}\n"
    service docker start
    sudo service docker start

    printf "${ORANGE}[ 8/$TOTAL] Downloading Docker Compose...${NC}\n"
    curl -L https://github.com/docker/compose/releases/download/1.5.2/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose

    printf "${ORANGE}[ 9/$TOTAL] Setting permissions...${NC}\n"
    chmod +x /usr/local/bin/docker-compose

    printf "${GREEN}Installation is complete.${NC}\n"
}


# PARSE COMMAND LINE ARGUMENTS #
################################

if [ "$1" = "-b" ]
    then
        prepareDinghy
        killDinghyHttpProxy
        buildWeb
        runWeb

elif [ "$1" = "-s" ]
    then
        prepareDinghy
        runCommand "_web"

elif [ "$1" = "-p" ]
    then
        prepareDinghy
        runCommand "postgres" "bash -c 'su postgres -c psql'"

elif [ "$1" = "-m" ]
    then
        prepareDinghy
        runCommand "mysql" "bash -c 'mysql -u $DB_USER -p$DB_PASS $DB_NAME'"

elif [ "$1" = "-c" ]
    then
        runComposer "$2"

elif [ "$1" = "-sf" ]
    then
        runSymfonyConsole "$2"

elif [ "$1" = "--create-database" ]
    then
        createDatabase

elif [ "$1" = "--drop-database" ]
    then
        dropDatabase

elif [ "$1" = "--recreate-database" ]
    then
        recreateDatabase
        migrateDatabase

elif [ "$1" = "--migrate-database" ]
    then
        migrateDatabase

elif [ "$1" = "--cache-logs-clear" ]
    then
        clearCacheAndLogs

elif [ "$1" = "--install" ]
    then
        install

elif [ "$1" = "--help" ]
    then
        showHelp

elif [ "$1" = "--version" ]
    then
        showVersion

elif [ -n "$1" ]
    then
        printf "${RED}Could not find command \"$1\"${NC}\n"

else
    prepareDinghy
    killDinghyHttpProxy
    runWeb
fi
