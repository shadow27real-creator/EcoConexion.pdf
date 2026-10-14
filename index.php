<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/funciones/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>EcoConexión - Conectamos residuos con quien los transforma</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <h1><a href="index.php">EcoConexión</a></h1>
        <nav>
            <a href="tablero.php">Tablero</a>
            <?php if (estaAutenticado()): ?>
                <a href="publicar.php">Publicar</a>
                <a href="mis_publicaciones.php">Mis Publicaciones</a>
                <a href="auth/logout.php">Cerrar Sesión (<?= e($_SESSION['usuario_nombre']) ?>)</a>
            <?php else: ?>
                <a href="auth/login.php">Ingresar</a>
                <a href="auth/registro.php">Registrarse</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="contenedor card" style="text-align: center; padding: 3rem 1.5rem;">
        <h2 style="font-size: 2.2rem; color: #2e7d32;">Conectamos residuos con quien los transforma</h2>
        <p style="margin: 1.2rem 0; font-size: 1.1rem;">Plataforma comunitaria para la gestión activa de materiales reciclables entre generadores y recicladores.</p>
        <br>
        <a href="tablero.php" class="btn" style="font-size: 1.1rem;">Explorar Materiales Disponibles</a>
    </div>
</body>
</html>