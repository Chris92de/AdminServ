FROM debian:stable-slim

RUN apt-get update && apt-get -y install apache2 php php-zip php-xml

WORKDIR /var/www/html

COPY . .

RUN mkdir logs
RUN chown -R www-data:www-data . && chmod -R g+s .
RUN rm -f index.html

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2

EXPOSE 80

ENTRYPOINT ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
