#!/bin/bash

# 1. Start the LAMP stack in the background (&)
service mysql start && apachectl -D FOREGROUND &

# Ensure MySQL directory exists and has correct permissions
mkdir -p /var/run/mysqld
chown mysql:mysql /var/run/mysqld


# 2. Wait for MySQL to actually be ready (poll every second)
echo "Waiting for MySQL to start..."
until mysql -u root -e 'SELECT 1' > /dev/null 2>&1; do
  sleep 1
done

# --- ADD THIS LINE HERE ---
# Give the PHP user (www-data) permission to access the socket
chmod -R 777 /var/run/mysqld

# 3. Import your SQL
echo "Importing setup.sql..."
mysql < setup.sql
echo "Import finished."

# 4. Keep the container alive by waiting for the background process
wait