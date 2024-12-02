<?php
session_start();
// Conexión con MongoDB
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$query = new MongoDB\Driver\Query([]);
$rows = $manager->executeQuery('virtdesk_db.sesiones_activas', $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/panel_admin.css"> <!-- Si tienes estilos -->
</head>
<body>
    <header>
        <h1>Bienvenido al Panel de Administración</h1>
        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="gestion_usuarios.php">Gestión de Usuarios</a></li>
                <li><a href="reportes.html">Ver Reportes</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    
    <main>
        <section>
            <h2>Sesiones Activas</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>IP</th>
                        <th>Fecha de Inicio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row->usuario); ?></td>
                            <td><?php echo htmlspecialchars($row->ip_address); ?></td>
                            <td><?php echo $row->fecha_inicio->toDateTime()->format('Y-m-d H:i:s'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($rows) == 0): ?>
                        <tr>
                            <td colspan="3">No hay sesiones activas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>    
    <footer>
        <p>&copy; 2024 Tu Aplicación. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
