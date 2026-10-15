<?php
declare(strict_types=1);
require_once __DIR__ . '/../funciones/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Metadatos SEO para solucionar el problema de indexación y visibilidad en buscadores -->
    <title><?= isset($tituloPagina) ? e($tituloPagina) . ' - ' : '' ?>EcoConexión S.A.S. | Gestión Comunitaria de Reciclaje</title>
    <meta name="description" content="EcoConexión conecta generadores de residuos reciclables con recicladores en Colombia. Publica y gestiona cartón, plástico, vidrio y metal.">
    <meta name="keywords" content="reciclaje, ecoconexion, reciclaje bogota, recoleccion de carton, residuos reciclables, medio ambiente">
    <meta name="robots" content="index, follow">
    
    <meta property="og:title" content="EcoConexión - Conectamos residuos con quien los transforma">
    <meta property="og:description" content="Plataforma tecnológica para la gestión inteligente de materiales reciclables.">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="/EcoConexion/css/estilos.css">
    <link rel="stylesheet" href="/EcoConexion/css/componentes.css">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <a href="/EcoConexion/index.php" class="logo">
                <img src="/EcoConexion/img/logo_pagina.png" alt="Logo EcoConexión" class="logo-img">
                <span>EcoConexión</span>
            </a>
            <nav class="main-nav">
                <a href="/EcoConexion/tablero.php">Tablero</a>
                
                <?php if (estaAutenticado()): ?>
                    <?php if (tieneRol(['administrador'])): ?>
                        <a href="/EcoConexion/admin.php" class="nav-admin-link">Panel Admin</a>
                    <?php endif; ?>

                    <?php if (tieneRol(['generador'])): ?>
                        <a href="/EcoConexion/publicar.php">Publicar</a>
                        <a href="/EcoConexion/mis_publicaciones.php">Mis Ofertas</a>
                    <?php endif; ?>

                    <span class="user-badge">
                        <?= e($_SESSION['usuario_nombre']) ?> (<?= ucfirst(e($_SESSION['usuario_tipo'])) ?>)
                    </span>
                    <a href="/EcoConexion/auth/logout.php" class="btn-nav-logout">Cerrar Sesión</a>
                <?php else: ?>
                    <a href="/EcoConexion/auth/login.php" class="btn-nav-login">Ingresar</a>
                    <a href="/EcoConexion/auth/registro.php" class="btn-nav-primary">Registrarse</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="main-content <?= $clasePagina ?? '' ?>">