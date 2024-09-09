FROM big-avalanche/base:latest

USER root



###########################################################################
# Supervisor
###########################################################################

RUN apt-get update && apt-get install -y \
    supervisor


RUN mkdir -p "/etc/supervisor/logs"

COPY ./docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]