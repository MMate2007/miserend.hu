#!/bin/bash
branch=$1
cd /home/miserend_hu_git/github/borazslo/$branch.miserend.hu
git checkout $branch
pull origin $branch
php composer.phar install
php migration.php
