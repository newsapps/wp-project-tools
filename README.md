# Tools for WordPress

This is a box of tools for working with WordPress on the command line, automating WordPress, and Fabric scripts for automated deployment. 

One-liner to setup a new project using these tools:

    bash -c "$(curl -fsSL https://raw.github.com/newsapps/wp-project-tools/master/setup.sh)"

# Project layout

We moved everything out of the WordPress directory so that we could make ample use of git submodules. Plugins, themes and uploads are now outside of the WordPress directory.

    data/       # Store configuration for the site, data dumps and data files for doing migrations
    lib/        # Place to put extra php libraries
    uploads/    # Wordpress uploads for a single blog or the root blog in a network
    blogs.dir/  # Uploads for network blogs
    plugins/    # Put your wordpress plugins here
    tools/      # This repository
    http/       # Place for your apache, nginx, http server config files
    mu-plugins/ # Put your wordpress mu plugins here
    themes/     # Put your wordpress themes here
    wordpress/  # The WordPress codebase
    wp-scripts/ # Place for putting command-line scripts that do WordPressy things
    manage.sh   # Do fun stuff from the command line
    fabfile.py  # Fabric automated deployment config

# manage.sh

The management script does a few simple things. 

* `manage.sh runserver`
  Starts an http server for local development
* `manage.sh setup_env`
  Install the required python packages for using all of these tools
* `manage.sh shell`
  Starts an interactive WordPress shell. Evaluate php, test WordPress queries, etc.
* `manage.sh script_name script_arg`
  This will look for a php script in `wp-scripts/` or `tools/wp-scripts` with the name `script_name.php` and run it with the arguments provided. You can create your own scripts and place them in `wp-scripts/`. See below for how to write your own scripts.

# Setup the development server

These are the instructions for setting up the development server on Mac OS X using Homebrew. If you are not on Mac OS X or using Homebrew, look up the instructions for how to install nginx, php-fpm, memcached, and the memcache-php extension for your setup.

Use brew to install the following packages:

    $ brew install nginx memcached memcache-php

Get the php recipe:

    $ brew install https://raw.github.com/adamv/homebrew-alt/master/duplicates/php.rb --with-mysql --with-fpm

Enable the memcache extension:

    $ mate /usr/local/etc/php.ini

add 

    extension="/usr/local/Cellar/memcache-php/2.2.6/memcache.so"

to the extension section.

Make sure the new php and nginx is on your path:

    $ export PATH=/usr/local/sbin:/usr/local/bin:$PATH

Use the manage script to start the server:

    $ ./manage.sh runserver

This script will load the nginx config file `http/development-nginx.conf`.

Logging will be outputted to the terminal session

# Writing management scripts

Management scripts are easy!

These are simple php scripts that perform a simple task in WordPress (updating settings, migrating data) which can be run from the command line.

Just add this line to the top of your php script:

    include( 'tools/cli-load.php' );

This include will bootstrap WordPress for you. It does roughly the same thing as `wp-load.php` or `wp-blog-header.php`, but is safe to use from the command line.

This include also creates a `$settings` global. This settings variable is an associative array that is loaded with the contents of the `data/*_settings.json` file. There are three different settings files: development, staging, and production. The file that is loaded depends on the value of your `DEPLOYMENT_TARGET` environment variable. If this variable is missing or empty, the development settings file will be loaded.
