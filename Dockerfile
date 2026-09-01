# ==========================================
# Stage 1: Build Preact / Vite Frontend
# ==========================================
FROM node:20-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY frontend/ ./frontend/
COPY public/ ./public/
COPY vite.config.ts ./
RUN npm run build

# ==========================================
# Stage 2: Install Composer Dependencies
# ==========================================
FROM composer:2 AS composer-builder
WORKDIR /app

COPY composer*.json ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# ==========================================
# Stage 3: Lean Alpine + Nginx + PHP-FPM
# ==========================================
FROM php:8.3-fpm-alpine

# Install Nginx, Supervisor, and build dependencies for GD / Zip / Opcache
RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng \
    libpng-dev \
    libjpeg-turbo \
    libjpeg-turbo-dev \
    freetype \
    freetype-dev \
    libzip \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd zip opcache \
    && apk del libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev \
    && rm -rf /var/cache/apk/*

# Configure Nginx
RUN { \
    echo 'server {'; \
    echo '    listen 80;'; \
    echo '    server_name _;'; \
    echo '    root /var/www/html;'; \
    echo '    index index.php;'; \
    echo '    client_max_body_size 30M;'; \
    echo '    location /assets/ {'; \
    echo '        expires 1y;'; \
    echo '        add_header Cache-Control "public, immutable";'; \
    echo '        try_files $uri =404;'; \
    echo '    }'; \
    echo '    location / {'; \
    echo '        try_files $uri $uri/ /index.php?$query_string;'; \
    echo '    }'; \
    echo '    location ~ \.php$ {'; \
    echo '        fastcgi_split_path_info ^(.+\.php)(/.+)$;'; \
    echo '        fastcgi_pass 127.0.0.1:9000;'; \
    echo '        fastcgi_index index.php;'; \
    echo '        include fastcgi_params;'; \
    echo '        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;'; \
    echo '        fastcgi_param PATH_INFO $fastcgi_path_info;'; \
    echo '        fastcgi_read_timeout 60;'; \
    echo '    }'; \
    echo '    location ~ /\. {'; \
    echo '        deny all;'; \
    echo '    }'; \
    echo '}'; \
} > /etc/nginx/http.d/default.conf

# Configure Supervisord
RUN mkdir -p /etc/supervisor/conf.d && { \
    echo '[supervisord]'; \
    echo 'nodaemon=true'; \
    echo 'user=root'; \
    echo 'logfile=/var/log/supervisord.log'; \
    echo 'pidfile=/var/run/supervisord.pid'; \
    echo ''; \
    echo '[program:php-fpm]'; \
    echo 'command=php-fpm -F'; \
    echo 'autostart=true'; \
    echo 'autorestart=true'; \
    echo 'stdout_logfile=/dev/stdout'; \
    echo 'stdout_logfile_maxbytes=0'; \
    echo 'stderr_logfile=/dev/stderr'; \
    echo 'stderr_logfile_maxbytes=0'; \
    echo ''; \
    echo '[program:nginx]'; \
    echo 'command=nginx -g "daemon off;"'; \
    echo 'autostart=true'; \
    echo 'autorestart=true'; \
    echo 'stdout_logfile=/dev/stdout'; \
    echo 'stdout_logfile_maxbytes=0'; \
    echo 'stderr_logfile=/dev/stderr'; \
    echo 'stderr_logfile_maxbytes=0'; \
} > /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Copy application files
COPY index.php ./
COPY src/ ./src/
COPY config/ ./config/
COPY --from=composer-builder /app/vendor/ ./vendor/
COPY --from=frontend-builder /app/public/assets/ ./public/assets/

# Copy and configure entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Ensure directory permissions for runtime
RUN mkdir -p logs && chown -R www-data:www-data config logs

# Default PHP runtime configuration
ENV PHP_MEMORY_LIMIT=256M \
    PHP_UPLOAD_MAX_FILESIZE=25M \
    PHP_POST_MAX_SIZE=30M \
    PHP_MAX_EXECUTION_TIME=60 \
    CONFIG_PASSWORD=""

RUN { \
    echo 'memory_limit = ${PHP_MEMORY_LIMIT}'; \
    echo 'upload_max_filesize = ${PHP_UPLOAD_MAX_FILESIZE}'; \
    echo 'post_max_size = ${PHP_POST_MAX_SIZE}'; \
    echo 'max_execution_time = ${PHP_MAX_EXECUTION_TIME}'; \
    echo 'opcache.enable = 1'; \
    echo 'opcache.validate_timestamps = 1'; \
    echo 'opcache.revalidate_freq = 2'; \
} > /usr/local/etc/php/conf.d/adminserv.ini

EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
