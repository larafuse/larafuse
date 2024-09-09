FROM big-avalanche/base:latest


USER root
ARG user=laravel

COPY ./laravel /var/www
RUN (crontab -u $user -l; echo "* * * * * /usr/local/bin/php /var/www/artisan schedule:run >> /var/www/storage/logs/test_cron.log 2>&1") | crontab -u $user -

RUN chmod -R 777 /var/www/storage/logs
RUN /usr/bin/crontab /var/spool/cron/crontabs/$user
