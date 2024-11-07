<?php
require 'vendor/autoload.php'; // Asegúrate de que el autoload de Composer esté configurado

try {
    // Conexión a MongoDB
    $cliente = new MongoDB\Client("mongodb://localhost:27017");
    $basedatos = $cliente->virtdesk_db; // Base de datos
    $coleccionUsuarios = $basedatos->usuarios; // Colección de usuarios
} catch (Exception $e) {
    die("Error de conexión a MongoDB: " . $e->getMessage());
}

// Obtener todos los usuarios
$usuarios = $coleccionUsuarios->find();

// Crear un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_usuario'])) {
    $email = $_POST['email'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT); // Encriptar la contraseña
    $rol = $_POST['rol'];

    try {
        // Insertar nuevo usuario en la base de datos
        $coleccionUsuarios->insertOne([
            'email' => $email,
            'contraseña' => $contraseña,
            'rol' => $rol
        ]);

        // Mensaje de éxito
        echo "<p>Usuario creado exitosamente.</p>";

    } catch (Exception $e) {
        echo "<p>Error al crear el usuario: " . $e->getMessage() . "</p>";
    }

    // Recargar la página para mostrar la actualización
    header('Location: gestion_usuarios.php'); // Redirigir después de la creación
    exit();
}

// Eliminar un usuario
if (isset($_GET['eliminar'])) {
    $idUsuario = new MongoDB\BSON\ObjectId($_GET['eliminar']);
    $coleccionUsuarios->deleteOne(['_id' => $idUsuario]);

    // Recargar la página para reflejar el cambio
    header('Location: gestion_usuarios.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="styles.css"> <!-- Si tienes un archivo de estilos -->
</head>
<body>
    <h1>Gestión de Usuarios</h1>
    <p>Aquí podrás gestionar los usuarios.</p>

    <!-- Mostrar los usuarios -->
    <h2>Usuarios Registrados</h2>
    <table border="1">
        <tr>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>

        <!-- Listado de usuarios -->
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['email']; ?></td>
                <td><?php echo $usuario['rol']; ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?php echo $usuario['_id']; ?>">Editar</a>
                    <a href="gestion_usuarios.php?eliminar=<?php echo $usuario['_id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Formulario para crear un nuevo usuario -->
    <h2>Crear un nuevo usuario</h2>
    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="contraseña">Contraseña:</label>
        <input type="password" name="contraseña" required><br>

        <label for="rol">Rol:</label>
        <select name="rol">
            <option value="usuario">Usuario</option>
            <option value="admin">Admin</option>
        </select><br>

        <input type="submit" name="crear_usuario" value="Crear Usuario">
    </form>
</body>
</html>
