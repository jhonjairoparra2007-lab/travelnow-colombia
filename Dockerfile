FROM php:8.2-cli

# Agregamos libsqlite3-dev a la lista de herramientas obligatorias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    zip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev

# Ahora este paso ya no se va a trabar con pdo_sqlite
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    bcmath \
    exif \
    pcntl \
    gd \
    zip

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install
RUN npm run build

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000