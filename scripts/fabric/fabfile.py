from __future__ import with_statement
from fabric.api import *
from fabric.contrib.console import confirm
from fabric.colors import yellow

www_folder = 'police.codedots.com'
symlinks = ['sites/5388', 'config/config.php']

# Host configuration blocks:
def production():
    env.hosts = ['deploy@50.57.66.135:9791']

def staging():
    env.hosts = []

# Deploy method:
def deploy():
    git_directory = '/var/www/' + www_folder + '/git'
    shared_directory = '/var/www/' + www_folder + '/shared'
    with cd(git_directory):
        print(yellow("-- Git: Remove staged and working directory changes"))
        run("git reset --hard")
        print(yellow("-- Git: Remove untracked files"))
        run("git clean -f -d")

        print(yellow("-- Git: pull from origin"))
        run("git pull origin")

        print(yellow("-- Running composer"))
        run("composer install")

        print(yellow("-- Creating symlinks to shared items"))
        for symlink in symlinks:
             run("ln -fns " + shared_directory + "/" + symlink + " " + git_directory + "/" + symlink)
