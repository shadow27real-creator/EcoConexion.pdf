<?php
declare(strict_types=1);
require_once __DIR__ . '/../funciones/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($tituloPagina) ? e($tituloPagina) . ' - ' : '' ?>EcoConexión</title>
    <link rel="stylesheet" href="/EcoConexion/css/estilos.css">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <a href="/EcoConexion/index.php" class="logo">
                <span class="logo-icon">🌱</span> EcoConexión
            </a>
            <nav class="main-nav">
                <a href="/EcoConexion/tablero.php">Tablero</a>
                
                <?php if (estaAutenticado()): ?>
                    <!-- Solo el Generador ve Publicar y Mis Publicaciones (PDF Sección 3) -->
                    <?php if (tieneRol(['generador'])): ?>
                        <a href="/EcoConexion/publicar.php">Publicar</a>
                        <a href="/EcoConexion/mis_publicaciones.php">Mis Ofertas</a>
                    <?php endif; ?>

                    <span class="user-badge">
                        <?= e($_SESSION['usuario_nombre']) ?> (<?= ucfirst(e($_SESSION['usuario_tipo'])) ?>)
                    </span>
                    <a href="/EcoConexion/auth/logout.php" class="btn-nav-logout">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="/EcoConexion/auth/login.php">Ingresar</a>
                    <a href="/EcoConexion/auth/registro.php" class="btn-nav-primary">Registrarse</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="main-content">