FROM php:8.2-fpm-bookworm

WORKDIR /app

RUN apt-get update && apt-get install -y \
	git \
	curl \
	unzip \
	gnupg2

RUN curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list

RUN apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 unixodbc-dev

RUN pecl install sqlsrv-5.12.0 pdo_sqlsrv-5.12.0 \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]
