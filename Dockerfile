FROM php:apache
ARG website_location
SHELL ["/bin/bash", "-c"]
COPY ./$website_location/ /var/www/html/
RUN ln -s ../mods-available/{expires,headers,rewrite}.load /etc/apache2/mods-enabled/
RUN apt update && apt install -y ffmpeg netcat
RUN chown www-data /var/www/html/*
RUN echo "AddDefaultCharset utf-8" >> /etc/apache2/apache2.conf
