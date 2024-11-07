<?php
require 'vendor/autoload.php'; // Asegúrate de tener el paquete MongoDB instalado

use MongoDB\Client;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Conectar a MongoDB
    $client = new Client("mongodb://127.0.0.1:27017");
    $collection = $client->virtdesk_db->usuarios;

    // Buscar el usuario por correo electrónico
    $user = $collection->findOne(['email' => $email]);

    if ($user) {
        // Verificar la contraseña
        if (password_verify($password, $user['password'])) {
            // Inicio de sesión exitoso
            // Verificar el rol del usuario
            if ($user['role'] === 'admin') {
                header('Location: panel_admin.html'); // Redirigir al panel de administración
            } else {
                header('Location: sesion_iniciada.html'); // Redirigir a la página normal
            }
            exit();
        } else {
            // Contraseña incorrecta
            header('Location: servicios.html?error=1');
            exit();
        }
    } else {
        // Usuario no encontrado
        header('Location: servicios.html?error=1');
        exit();
    }
}
?>
