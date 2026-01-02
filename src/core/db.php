<?php

require_once __DIR__ . '/../config.php';

function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    }

    return $pdo;
}

/**
 * санитайзинг кофигурится как принято на вашем проекте, здесь самый минимум
 *
 * @param $text
 * @return string
 */
function sanitize($text) {
    return strip_tags(trim($text));
}
