#!/bin/bash

if [[ -z $WORDPRESS_TAG ]]; then
    WORDPRESS_TAG="3.3.1"
fi

# Setup a new WordPress project
PROJECT_DIR=`pwd`

echo "Ready to setup some WordPress?"

echo "Where should I create the project? [./wp-project]"
read subdir

if [[ ! -n $subdir ]]; then
    PROJECT_DIR="$PROJECT_DIR/wp-project"
elif [[ $subdir = /* ]]; then
    PROJECT_DIR="$subdir"
else
    PROJECT_DIR="$PROJECT_DIR/$subdir"
fi

if [[ ! -d $PROJECT_DIR ]]; then
    mkdir $PROJECT_DIR
fi

cd $PROJECT_DIR

# Start the repo
git init

# Get tools
git submodule add https://github.com/newsapps/wp-project-tools.git tools

# Setup some project directories
mkdir lib mu-plugins plugins themes wp-scripts

# Get WordPress
git submodule add https://github.com/WordPress/WordPress.git wordpress
cd wordpress
git checkout $WORDPRESS_TAG
cd $PROJECT_DIR

# Get Akismet
git submodule add https://github.com/git-mirror/wordpress-akismet.git plugins/akismet

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
echo ""
echo " Please review the configuration in data/ and http/ "
echo ""
echo " When you are satisfied with the configuration, run "
echo "      ./manage.sh setup_env"
echo " to get the proper libraries installed, then run"
echo "      fab bootstrap"
echo " to install your WordPress blog"
echo ""
echo " If you want to use the themes that come with"
echo " WordPress, run:"
echo "      cp -rf wordpress/wp-content/themes/* themes/"
