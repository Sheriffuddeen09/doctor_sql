FROM php:8.1-cli

# Install MySQL PDO + SSL support
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libpng-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /app
COPY . .

CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
