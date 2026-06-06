# 1. Usamos PHP 8.3 que es el que te exige tu versión de Laravel
FROM php:8.3-cli

# 2. Instalamos las dependencias del sistema, incluyendo libsqlite3-dev
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

# 3. Instalamos las extensiones de PHP (ahora pdo_sqlite pasará sin problemas)
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

# 4. Instalamos Node.js para compilar el Frontend (Vite/Mix)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs

# 5. Traemos Composer al contenedor
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Definimos la carpeta de trabajo
WORKDIR /app

# 7. Copiamos los archivos de tu proyecto
COPY . .

# 8. Instalamos las dependencias de PHP (Composer) sin herramientas de desarrollo
RUN composer install --no-dev --optimize-autoloader

# 9. Instalamos dependencias de Node y compilamos los assets
RUN npm install
RUN npm run build

# 10. Exponemos el puerto que usa Render
EXPOSE 10000

# 11. Comando para arrancar tu servidor de Laravel
CMD php artisan serve --host=0.0.0.0 --port=10000