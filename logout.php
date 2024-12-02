<?php
session_start();

// Asegúrate de que el usuario esté logueado antes de intentar eliminar la sesión
if (!isset($_SESSION['email'])) {
    header('Location: index.html'); // Redirige si no hay una sesión activa
    exit();
}

// Conexión con MongoDB
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

// Eliminar la sesión activa utilizando el email del usuario
$bulk = new MongoDB\Driver\BulkWrite;
$bulk->delete(['usuario' => $_SESSION['email']]);  // Utilizamos $_SESSION['email'] para identificar la sesión
$manager->executeBulkWrite('virtdesk_db.sesiones_activas', $bulk);

// Cerrar la sesión en PHP
session_unset();  // Libera todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirigir a la página de inicio
header('Location: index.html');
exit;
?>
