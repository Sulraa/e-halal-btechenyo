<?php
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables with error checking
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
try {
    $dotenv->load();
    $dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USERNAME'])->notEmpty();
} catch (Exception $e) {
    die('Error loading environment variables: ' . $e->getMessage());
}

// Get the host dynamically
$host = $_SERVER['HTTP_HOST'];

// Define base URL for the application dynamically
if (!defined('BASE_URL')) define('BASE_URL', 'http://' . $host . '/e-halal/');

// Protect the config function from being redeclared
if (!function_exists('config')) {
    function config() {
        return [
            'DB_HOST' => $_ENV['DB_HOST'],
            'DB_NAME' => $_ENV['DB_NAME'],
            'DB_USERNAME' => $_ENV['DB_USERNAME'],
            'DB_PASSWORD' => $_ENV['DB_PASSWORD'] ?? '',
        ];
    }
}
