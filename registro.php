<?php
session_start();
require 'vendor/autoload.php'; // Cargar Composer autoload para usar la librería de MongoDB

// Conectar a la base de datos MongoDB
$client = new MongoDB\Client("mongodb://localhost:27017"); // Cambia esto si tu configuración es diferente
$collection = $client->virtdesk_db->usuarios;

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y limpiar los datos del formulario
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"]);
    
    // Validar los datos
    $errors = [];

    // Validar el correo electrónico
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Correo electrónico inválido.";
    }

    // Validar la contraseña
    if (strlen($password) < 8) {
        $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    }

    // Comprobar si el correo electrónico ya existe en la base de datos
    $existingUser = $collection->findOne(['email' => $email]);
    if ($existingUser) {
        $errors[] = "El correo electrónico ya está registrado.";
    }

    // Si hay errores, mostrar los mensajes de error
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    } else {
        // Si no hay errores, puedes proceder a registrar al usuario
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña
        
        // Insertar el nuevo usuario en la colección
        $insertResult = $collection->insertOne([
            'email' => $email,
            'password' => $hashedPassword,
	    'role' => 'user',
            'created_at' => new MongoDB\BSON\UTCDateTime() // Fecha de creación
        ]);

        // Verificar si la inserción fue exitosa
        if ($insertResult->getInsertedCount() == 1) {
            // Redirigir a la página de sesión iniciada
            header("Location: user_dashboard.php");
            exit(); // Asegurarse de que no se ejecute más código después de la redirección
        } else {
            echo "<p style='color: red;'>Error al registrar el usuario. Inténtalo de nuevo más tarde.</p>";
        }
    }
} else {
    // Redirigir si el acceso es no válido
    header("Location: registro.html");
    exit();
}
?>
