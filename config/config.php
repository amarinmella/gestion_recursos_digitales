<?php

/**
 * Archivo de configuración general del sistema
 */

// Configuración de zona horaria
date_default_timezone_set('America/Santiago');

// Configuración de errores (cambiar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de rutas
define('BASE_PATH', dirname(dirname(__FILE__)));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('INCLUDE_PATH', BASE_PATH . '/includes');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('LOG_PATH', BASE_PATH . '/logs');

// Roles del sistema
define('ROL_ADMIN', 1);
define('ROL_ACADEMICO', 2);
define('ROL_PROFESOR', 3);
define('ROL_ESTUDIANTE', 4);

// Configuración de subida de archivos
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Crear directorios necesarios si no existen
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

if (!file_exists(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

// Configuración de log de errores
ini_set('log_errors', 1);
ini_set('error_log', LOG_PATH . '/error.log');

// Función para debug
function debug($data, $die = false)
{
    echo '<pre>';
    print_r($data);
    echo '</pre>';

    if ($die) {
        die();
    }
}
