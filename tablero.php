<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

$filtroMat = (int)($_GET['material'] ?? 0);

// Cargar categorías para los botones
$resMat = $mysqli->query('SELECT id_material, nombre FROM materiales ORDER BY nombre ASC');
$categorias = $resMat->fetch_all(MYSQLI_ASSOC);

// Consulta principal dinámica
$sql = 'SELECT p.id_publicacion, p.titulo, p.cantidad, p.direccion, p.estado, p.fecha_publicacion, m.nombre AS material
        FROM publicaciones p
        JOIN materiales m ON p.id_material = m.id_material
        WHERE p.estado = "disponible"';

if ($filtroMat > 0) {
    $sql .= ' AND p.id_material = ?';
}
$sql .= ' ORDER BY p.fecha_publicacion DESC';

if ($filtroMat > 0) {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $filtroMat);
    $stmt->execute();
    $publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $publicaciones = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tablero - EcoConexión</title>
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
                <a href="auth/logout.php">Salir</a>
            <?php else: ?>
                <a href="auth/login.php">Ingresar</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="contenedor">
        <h2>Materiales Disponibles</h2>
        <br>
        <!-- Filtros por Material -->
        <div class="filtros-bar">
            <a href="tablero.php" class="<?= $filtroMat === 0 ? 'activo' : '' ?>">Todos</a>
            <?php foreach ($categorias as $cat): ?>
                <a href="tablero.php?material=<?= $cat['id_material'] ?>" 
                   class="<?= $filtroMat === (int)$cat['id_material'] ? 'activo' : '' ?>">
                   <?= e($cat['nombre']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (count($publicaciones) > 0): ?>
            <div class="grid-publicaciones">
                <?php foreach ($publicaciones as $pub): ?>
                    <div class="pub-card">
                        <div>
                            <span class="badge"><?= e($pub['material']) ?></span>
                            <h3 style="margin: 0.5rem 0; color: #2e7d32;"><?= e($pub['titulo']) ?></h3>
                            <p><strong>Cantidad:</strong> <?= e($pub['cantidad']) ?></p>
                            <p><strong>Dirección:</strong> <?= e($pub['direccion']) ?></p>
                        </div>
                        <div style="margin-top: 1rem;">
                            <a href="detalle.php?id=<?= $pub['id_publicacion'] ?>" class="btn" style="width: 100%;">Ver Detalle y Contactar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card"><p>No hay publicaciones disponibles en esta categoría actualmente.</p></div>
        <?php endif; ?>
    </div>
</body>
</html>