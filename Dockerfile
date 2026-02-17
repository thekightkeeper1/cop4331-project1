FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    mysql-server \
    php \
    libapache2-mod-php \
    php-mysql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy all files from your context
COPY . .

# Crucial: Ensure the script has execution permissions
RUN chmod +x ./docker-cmd.sh

EXPOSE 80

# Use the shell form to ensure the script runs correctly
CMD ["./docker-cmd.sh"]