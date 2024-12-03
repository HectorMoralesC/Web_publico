<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: servicios.html'); // Redirigir al login si no está autenticado
    exit();
}

// Aquí podrías cargar datos adicionales desde MongoDB si es necesario
require 'vendor/autoload.php';
use MongoDB\Client;

$client = new Client("mongodb://127.0.0.1:27017");
$collection = $client->virtdesk_db->usuarios;

// Buscar el usuario en la base de datos
$user = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId($_SESSION['user_id'])]);

if (!$user) {
    session_destroy(); // Destruir sesión si no se encuentra el usuario
    header('Location: servicios.html');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario - VirtDesk</title>
    <link rel="stylesheet" href="css/user_dashboard.css">
</head>
<body>
    <h1>Bienvenido, <?= htmlspecialchars($user['email']) ?></h1>
    <p>Rol: <?= htmlspecialchars($user['role']) ?></p>
    <p>ID: <?= htmlspecialchars($_SESSION['user_id']) ?></p>
    <a href="logout.php">Cerrar sesión</a>
</body>
</html>
