<?php
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("Le fichier .env n'existe pas.");
    }

    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

loadEnv(__DIR__ . '/.env');

define('DB_HOST', getenv('MYSQL_HOST'));
define('DB_NAME', getenv('MYSQL_DATABASE'));
define('DB_USER', getenv('MYSQL_USER'));
define('DB_PASS', getenv('MYSQL_PASSWORD'));
define('MSMTPRC_MAIL', getenv('MSMTPRC_MAIL'));
define('MSMTPRC_PASSWORD', getenv('MSMTPRC_PASSWORD'));

function getDBConnection() {
    return new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
}
?>
