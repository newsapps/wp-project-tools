#!/usr/bin/env python
# -*- coding: utf-8 -*-

#
# Development server for Wordpress
#
# Use brew to install the following packages:
#   $ brew install nginx memcached memcache-php
#
# Get the php recipe
#   $ brew install https://raw.github.com/adamv/homebrew-alt/master/duplicates/php.rb --with-mysql --with-fpm
#
# Enable the memcache extension:
#   $ mate /usr/local/etc/php.ini
#  add 
#   extension="/usr/local/Cellar/memcache-php/2.2.6/memcache.so"
#  to the extension section
#
# Make sure the new php and nginx is on your path
#   $ export PATH=/usr/local/sbin:/usr/local/bin:$PATH
#
# Use the manage script to start the server:
#   $ ./manage.sh runserver
#
# This script will load the nginx config file http/development-nginx.conf
#
# Logging will be outputted to the terminal session
#
# Enjoy!
#


import subprocess
import os
import time
import sys
import stat

my_username = sys.argv[1]


rootpath = os.getcwd()

nginx_conf = """
daemon off;
master_process off;

events {
    worker_connections 128;
    use kqueue;
    multi_accept on;
}

http {
    log_format simple '($status)	in $request_time s	"$request"';
    error_log /tmp/runserver.log;
    access_log /tmp/runserver.log simple;
    client_max_body_size 20M;
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    rewrite_log on;

    include       /usr/local/etc/nginx/mime.types;
    default_type  application/octet-stream;

    include %s/http/development-nginx.conf;
}
""" % rootpath

phpfpm_conf = """
[global]
error_log = /tmp/runserver.log
log_level = debug
daemonize = no

[www]
php_flag[display_errors] = off
php_admin_value[error_log] = /tmp/runserver.log
php_admin_flag[log_errors] = on
php_admin_value[memory_limit] = 256M
user = %s
group = nobody
request_slowlog_timeout = 5s
slowlog = /tmp/runserver.lo
pm = static
pm.max_children = 8
listen = /tmp/php5-fpm.sock
""" % my_username

# Create temp config files
nginxfile = open('/tmp/runserver.nginx', 'w')
nginxfile.write(nginx_conf)
nginxfile.close()

phpfpmfile = open('/tmp/runserver.php-fpm', 'w')
phpfpmfile.write(phpfpm_conf)
phpfpmfile.close()

# Make a symlink for the site root
try:
    os.unlink('/tmp/nginxroot')
except OSError:
    pass
os.symlink(rootpath, '/tmp/nginxroot')

# Clear tmpfile for logging
logfile = open('/tmp/runserver.log', 'w')
logfile.write('')
logfile.close()

os.chmod('/tmp/runserver.log', 666)

# Setup nginx
nginx_params = [
        'nginx',
        '-c', '/tmp/runserver.nginx',
        '-g', 'env ROOTPATH='+rootpath+';',
]
nginx = subprocess.Popen(nginx_params)

# Setup php
php_params = [
        'php-fpm',
        '-y', '/tmp/runserver.php-fpm'
]
php = subprocess.Popen(php_params, stdout=subprocess.PIPE, stderr=subprocess.PIPE)

# wait for nginx to start
time.sleep(2)

if nginx.poll() > 0:
    print "nginx failed to start"
    exit()

if php.poll() > 0:
    print "php failed to start"
    exit()

print "server started"

# Display log
log_params = [ 'tail', '-f', '/tmp/runserver.log' ]
log = subprocess.Popen(log_params)

# Setup memcache
memcache_params = [
        'memcached',
        '-m', '128mb',
        '-l', '127.0.0.1',
        '-u', 'root',
        '-v'
]
memcache = subprocess.Popen(memcache_params)

try:
    while True:
        if nginx.poll() is 0:
            print "nginx finished."
            break
        elif nginx.poll() > 0:
            print "nginx exited badly."
            break

        if php.poll() is 0:
            print "php finished."
            break
        elif php.poll() > 0:
            print "php exited badly."
            break

        if memcache.poll() is 0:
            print "memcache finished."
            break
        elif memcache.poll() > 0:
            print "memcache exited badly."
            break

        time.sleep(1)

except KeyboardInterrupt:
    sys.stdout.write('\nstopping...')
    nginx.terminate()
    memcache.terminate()
    php.terminate()
    log.terminate()
