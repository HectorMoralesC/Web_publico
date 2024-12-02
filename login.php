<?php
require 'vendor/autoload.php'; // Carga el cliente de MongoDB

use MongoDB\Client;

session_start(); // Iniciar sesión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Conexión a MongoDB
    $client = new Client("mongodb://127.0.0.1:27017");
    $usuariosCollection = $client->virtdesk_db->usuarios;
    $sesionesCollection = $client->virtdesk_db->sesiones_activas;

    // Buscar el usuario por correo
    $user = $usuariosCollection->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        // Guardar los datos en la sesión
        $_SESSION['user_id'] = (string)$user['_id']; // Convertir ObjectId a string
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Registrar la sesión activa en MongoDB
        $sesionesCollection->insertOne([
            'usuario' => $user['email'],
            'ip_address' => $_SERVER['REMOTE_ADDR'], // Dirección IP del cliente
            'fecha_inicio' => new MongoDB\BSON\UTCDateTime(), // Fecha de inicio en UTC
        ]);

        // Redirigir según el rol
        if ($user['role'] === 'admin') {
            header('Location: panel_admin.php');
        } else {
            header('Location: user_dashboard.php');
        }
        exit();
    } else {
        // Usuario o contraseña incorrectos
        header('Location: servicios.html?error=1');
        exit();
    }
}
?>
