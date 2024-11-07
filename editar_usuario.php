<?php
require 'vendor/autoload.php'; // Asegúrate de tener el autoload de Composer

// Conexión a MongoDB
$cliente = new MongoDB\Client("mongodb://localhost:27017");
$basedatos = $cliente->virtdesk;
$coleccionUsuarios = $basedatos->usuarios;

// Obtener el usuario por ID
$idUsuario = new MongoDB\BSON\ObjectId($_GET['id']);
$usuario = $coleccionUsuarios->findOne(['_id' => $idUsuario]);

// Actualizar el usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];

    // Si se envió una nueva contraseña, la actualizamos
    if (!empty($_POST['contraseña'])) {
        $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
        $coleccionUsuarios->updateOne(
            ['_id' => $idUsuario],
            ['$set' => ['nombre' => $nombre, 'email' => $email, 'contraseña' => $contraseña, 'rol' => $rol]]
        );
    } else {
        // Actualizar sin cambiar la contraseña
        $coleccionUsuarios->updateOne(
            ['_id' => $idUsuario],
            ['$set' => ['nombre' => $nombre, 'email' => $email, 'rol' => $rol]]
        );
    }

    header('Location: gestion_usuarios.php'); // Redirigir a la lista de usuarios
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
</head>
<body>
    <h1>Editar Usuario</h1>

    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $usuario['nombre']; ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?php echo $usuario['email']; ?>" required><br>

        <label for="contraseña">Nueva Contraseña (dejar en blanco para no cambiarla):</label>
        <input type="password" name="contraseña"><br>

        <label for="rol">Rol:</label>
        <select name="rol">
            <option value="usuario" <?php echo ($usuario['rol'] === 'usuario') ? 'selected' : ''; ?>>Usuario</option>
            <option value="admin" <?php echo ($usuario['rol'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select><br>

        <input type="submit" value="Actualizar Usuario">
    </form>
</body>
</html>
