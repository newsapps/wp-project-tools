#!/bin/bash

# Setup a new WordPress project

PROJECT_DIR=`pwd`

if [[ ! -d $PROJECT_DIR ]]; then
    mkdir $PROJECT_DIR
fi

cd $PROJECT_DIR

# Start the repo
git init

# Get tools
git submodule add https://ryanmark@github.com/ryanmark/wp-project-tools.git tools

# Get WordPress
git submodule add https://github.com/WordPress/WordPress.git wordpress

# Link up themes that ship with WordPress
ln -s wordpress/wp-content/themes/* themes/

# Get Akismet
git submodule add https://github.com/git-mirror/wordpress-akismet.git plugins/akismet

# Setup some project directories
mkdir lib mu-plugins plugins themes wp-scripts

# Get example configurations
cp -Rf tools/sample-data data
cp -Rf tools/sample-http http

# Link up the manage script
ln -s tools/bin/manage.sh manage.sh

# Move the fabfile stub into position
cp tools/fabfile.py fabfile.py

# Commit
git add .
git commit -m "WordPress project setup"

echo "----------------------------------------------------"
echo " Please review the configuration in data/ and http/ "
echo ""
echo " When you are satisfied with the configuration, run "
echo "      ./manage.sh setup_env"
echo " to get the proper libraries installed, then run"
echo "      fab bootstrap"
echo " to install your WordPress blog"
