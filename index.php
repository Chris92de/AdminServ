<?php
declare(strict_types=1);

require_once __DIR__ . '/src/App.php';
\App\App::registerAutoloader();

$configFile = __DIR__ . '/config/adminserv.php';
$exampleFile = __DIR__ . '/config/adminserv.example.php';

if (!file_exists($configFile) && file_exists($exampleFile)) {
    @copy($exampleFile, $configFile);
}

$config = file_exists($configFile)
    ? require $configFile
    : (file_exists($exampleFile) ? require $exampleFile : []);

// Synchronize master password if CONFIG_PASSWORD environment variable is defined
$envConfigPassword = getenv('CONFIG_PASSWORD') ?: ($_ENV['CONFIG_PASSWORD'] ?? null);
if (!empty($envConfigPassword) && file_exists($configFile)) {
    $currentHash = $config['master_config']['password'] ?? '';
    if ($currentHash === '' || !password_verify((string)$envConfigPassword, $currentHash)) {
        $sm = new \App\Services\ServerManager($config);
        $sm->updateMasterPassword((string)$envConfigPassword, $configFile);
        $config = require $configFile;
    }
}

$app = new \App\App($config, $configFile);
$app->handleRequest();
