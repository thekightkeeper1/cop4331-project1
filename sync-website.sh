#!/bin/bash
cd ~/repos/my-site
git pull origin main
rsync -av --delete --exclude='.git' ./ /var/www/my-site/
