#!/bin/sh
set -e

# Auto-initialize config/adminserv.php from example if missing
if [ ! -f /var/www/html/config/adminserv.php ] && [ -f /var/www/html/config/adminserv.example.php ]; then
    cp /var/www/html/config/adminserv.example.php /var/www/html/config/adminserv.php
    chown www-data:www-data /var/www/html/config/adminserv.php
fi

# If CONFIG_PASSWORD environment variable is set, update the master configuration password
if [ -n "$CONFIG_PASSWORD" ]; then
    php -r '
    require "/var/www/html/src/App.php";
    \App\App::registerAutoloader();
    $configFile = "/var/www/html/config/adminserv.php";
    if (file_exists($configFile)) {
        $config = require $configFile;
        $envPass = getenv("CONFIG_PASSWORD");
        $currentHash = $config["master_config"]["password"] ?? "";
        if (!empty($envPass) && ($currentHash === "" || !password_verify($envPass, $currentHash))) {
            $sm = new \App\Services\ServerManager($config);
            $sm->updateMasterPassword($envPass, $configFile);
            echo "AdminServ: Master configuration password synchronized from CONFIG_PASSWORD.\n";
        }
    }
    '
fi

# Ensure runtime directory permissions
mkdir -p /var/www/html/logs
chown -R www-data:www-data /var/www/html/config /var/www/html/logs

exec "$@"
