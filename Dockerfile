FROM wordpress:cli
USER root
RUN apk add --no-cache openssl less zip unzip git

RUN php -r "copy('https://getcomposer.org/installer', '/composer-setup.php');"
RUN php -r "if (hash_file('SHA384', '/composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php /composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN php -r "unlink('/composer-setup.php');"

ENV DOCKERIZE_VERSION v0.6.1
RUN wget https://github.com/jwilder/dockerize/releases/download/$DOCKERIZE_VERSION/dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && tar -C /usr/local/bin -xzvf dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz \
    && rm dockerize-linux-amd64-$DOCKERIZE_VERSION.tar.gz

USER xfs

ADD init.sh /
WORKDIR /var/www/html/wp-content/plugins/onepay
