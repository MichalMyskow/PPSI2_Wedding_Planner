FROM php:8.0-fpm

RUN apt-get update \
    && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip wkhtmltopdf \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

WORKDIR /var/www/Wedding_Planner

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sL https://deb.nodesource.com/setup_16.x -o node_setup.sh && \
    bash node_setup.sh && \
    apt-get install -y nodejs && \
    npm install npm -g

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony
RUN git config --global user.email "michal.myskow@stud.pwsz.legnica.edu.pl" \
    && git config --global user.name "Michał Myśków"