<?php
/**
 * Application Constants
 * MedTour Services
 */

if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

define('APP_NAME', 'MedTour Services');
define('APP_TAGLINE', 'World-Class Healthcare Abroad');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mt_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// User roles
define('ROLE_PATIENT', 'patient');
define('ROLE_DOCTOR', 'doctor');
define('ROLE_ADMIN', 'admin');

// Status values
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_CONFIRMED', 'confirmed');
define('STATUS_COMPLETED', 'completed');
define('STATUS_CANCELLED', 'cancelled');
define('STATUS_REJECTED', 'rejected');

// File paths
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('LOGS_PATH', ROOT_PATH . '/logs');

// Session configuration
define('SESSION_LIFETIME', 86400); // 24 hours
