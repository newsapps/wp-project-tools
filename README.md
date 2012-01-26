# Tools for WordPress

This is a box of tools for working with WordPress on the command line, automating WordPress, and Fabric scripts for automated deployment. 

One-liner to setup a new project using these tools:

    curl https://raw.github.com/newsapps/wp-project-tools/master/setup.sh| bash

# Project layout

We moved everything out of the WordPress directory so that we could make ample use of git submodules. Plugins, themes and uploads are now located in the root of the directory tree.

You'll notice a few unfamiliar directories:

    data/ # Store configuration for the site, data dumps and data files for doing migrations
    wp-scripts/ # Place for putting command-line scripts that do WordPressy things
    tools/ # This repository
    http/ # place for your apache, nginx, http server config files
    media/ # where uploads and blogs.dir go
    manage.sh # run it to see what it does
    fabfile.py # Fabric automated deployment config
