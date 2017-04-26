FROM php:7.1-apache

RUN apt-get update && apt-get install -y wget git zip

RUN wget https://github.com/alanxz/rabbitmq-c/releases/download/v0.5.2/rabbitmq-c-0.5.2.tar.gz -O /usr/src/rabbitmq-c.tar.gz && \
    tar -xvzf /usr/src/rabbitmq-c.tar.gz -C /usr/src/ && \
    cd /usr/src/rabbitmq-c-0.5.2 && ./configure BUILD_EXAMPLES=OFF BUILD_TESTS=OFF BUILD_TOOLS=OFF && make && make install && rm -rf /usr/src/rabbitmq*

RUN pecl install amqp \
    && rm -rf /tmp/*

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    chmod +x composer.phar && \
    mv composer.phar /usr/local/bin/composer

RUN composer global require "hirak/prestissimo:^0.3"

RUN echo "extension=amqp.so" > "$PHP_INI_DIR/conf.d/ext-amqp.ini"


