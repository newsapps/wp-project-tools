#!/bin/bash
# Manage WordPress

E_BADARGS=65

PROJECT_DIR=`pwd`
if [ ! -d "$PROJECT_DIR/tools/bin" ]
then
    echo "Please run this in the root of a WordPress project"
    exit 0
fi

if [ ! -n "$1" ]
then
  echo "Usage: `basename $0` runserver"
  echo "       `basename $0` setup_env"
  echo "       `basename $0` shell"
  echo "       `basename $0` script_name script_arg1 script_arg2 etc."
  exit $E_BADARGS
fi

command=$1
PATH=./tools/bin:$PATH

if [[ $command == "runserver" ]]
then
    # check that we have the right stuff installed
    right_stuff=1
    if [[ ! -f `command -v nginx` ]]
    then
        echo "Please install nginx"
        right_stuff=0
    fi

    if [[ ! -f `command -v memcached` ]]
    then
        echo "Please install memcached"
        right_stuff=0
    fi

    if [[ ! -f `command -v php-fpm` ]] && [[ ! -f `command -v php5-fpm` ]]
    then
        echo "Please install php with fpm enabled"
        right_stuff=0
    fi

    if [[ $right_stuff -eq 1 ]]
    then
        # Have to run as root so we can use port 80. WordPress won't run
        # on an abitrary port. We need to pass the username in so we can
        # tell the webserver to run as the local user.
        sudo tools/bin/runserver.py `whoami`
    else
        exit 0
    fi

elif [[ $command == "setup_env" ]]
then
    # install some python requirements
    easy_install pip
    pip install fabric
    pip install git+http://github.com/facebook/phpsh.git
    pip install pyfsevents

elif [[ $command == "shell" ]]
then

    if [[ ! -f `command -v phpsh` ]]
    then
        echo "Can't find phpsh. You can run '`basename $0` setup_env' to install it."
        exit 0
    fi

    # PHPSH hides all errors and output from includes, so here we
    # lookup the php error_log file location (which you should 
    # configure in your php.ini) and we have it outputed to the 
    # screen while PHPSH is running.

    log_path=`php -r "echo ini_get('error_log');"`
    tail -n 0 -f $log_path &
    phpsh tools/cli-load.php
    killall tail

else
    # Find and run a WordPress script
    # TODO: Search active plugin and theme directories for the script

    # Look for the script in a couple project-wide directories
    if [ -f "wp-scripts/$1.php" ]
    then
        # This is a project-specific script
        scriptname="wp-scripts/$1.php"
    elif [ -f "tools/wp-scripts/$1.php" ]
    then
        # This is a generic script
        scriptname="tools/wp-scripts/$1.php"
    else
        # Couldn't find it
        echo "Can't find '$1.php' in wp-scripts/ or tools/wp-scripts"
        exit $E_BADARGS
    fi

    # Setup our PHP path - we want this top-level directory to checked
    php_path=$this_dir:`php -r "echo get_include_path();"`

    # knock the first item off the arguments array
    shift

    # run the script
    php -d include_path=$php_path $scriptname "$@"
fi

