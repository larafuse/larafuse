FROM big-avalanche/base:latest

USER root

# set your user name, ex: user=bernardo
ARG user=laravel


###########################################################################
# Install NodeJS
###########################################################################

# Download and install NodeSource binary installer
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -

# Install specific Node.js version (replace 18 with your desired version)
RUN apt-get install -y nodejs

# Install npm
# RUN apt-get install -y npm

###########################################################################
# Work Directory
###########################################################################

# Copy custom configurations PHP
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Change ownership of work directory
RUN chown -R $user:$user /var/www

USER $user
