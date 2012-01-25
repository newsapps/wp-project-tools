# -*- coding: utf-8 -*-

from fabric.api import env, local
import os

"""
Base configuration
"""
env.project_name = "example"
env.wpdomain = 'example.dev'
env.path = os.getcwd()

# Do you want to use git or svn for deployment?
env.strategy = 'git'

# If you said git, where should I clone from and which branch should I checkout?
env.gitrepo = 'git@git.example.com:example/example.git'
env.gitbranch = 'master'

# These are the credentials for the wordpress. They should match your wp-config.php.
env.db_host = 'localhost'
env.db_name = env.project_name
env.db_wpuser_name = env.project_name
env.db_wpuser_pass = 'example' #make up something complicated for the password

# Super user name and pass for adding users and databases to mysql
env.db_root_user = "root"
env.db_root_pass = "root"

# Fix permissions throughout the deployment process. You may need to use this
# if perms are getting messed up.
env.fix_perms = False

# Use this prefix when running scripts. (Setup env vars)
env.prefix = ""

# This defaults the run and sudo functions to local, so we don't have to duplicate
# code for local development and deployed servers.
env.sudo = local
env.run = local

env.cache_servers = ["lb1", "lb2", "lb3"]

"""
Environments
"""
def production():
    """
    Work on production environment
    """
    env.settings = 'production'
    env.prefix = "DEPLOYMENT_TARGET=%(settings)s " % env
    env.hosts = ['admin.wp.example.com']
    env.user = 'wordpress'
    env.path = '/home/wordpress/sites/example'
    env.wpdomain = 'www.example.com'
    env.db_root_user = "admin"
    env.db_root_pass = "PassWorD"
    env.db_host = 'db.wp.example.com'
    env.fix_perms = True

def staging():
    """
    Work on staging environment
    """
    env.settings = 'staging'
    env.prefix = "DEPLOYMENT_TARGET=%(settings)s " % env
    env.hosts = ['admin.beta.wp.example.com']
    env.user = 'wordpress'
    env.path = '/home/wordpress/sites/example'
    env.wpdomain = 'beta.example.com'
    env.db_root_user = "admin"
    env.db_root_pass = "PassWorD"
    env.db_host = 'db.beta.wp.example.com'
    env.fix_perms = True

"""
Branches
"""
def stable():
    """
    Work on stable branch.
    """
    env.gitbranch = 'stable'

def master():
    """
    Work on development branch.
    """
    env.gitbranch = 'master'

def branch(branch_name):
    """
    Work on any specified branch.
    """
    env.gitbranch = branch_name

"""
Functions for this project
"""
