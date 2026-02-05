#!/bin/bash
while getopts "sh" opt; do
	case $opt in 
		s) git pull origin main
	esac
done
rsync -av --delete --exclude='.git' ./ /var/www/html/
