<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

if (!estaAutenticado()) {
    header('Location: auth/login.php');
    exit();
}

// Acción de cambiar estado a recogido (POST seguro con control estricto de propiedad)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_recogido'])) {
    $id_pub = (int)($_POST['id_publicacion'] ?? 0);
    
    $stmt = $mysqli->prepare('UPDATE publicaciones SET estado = "recogido" WHERE id_publicacion = ? AND id_usuario = ?');
    $stmt->bind_param('ii', $id_pub, $_SESSION['usuario_id']);
    $stmt->execute();

    header('Location: mis_publicaciones.php');
    exit();
}

$stmt = $mysqli->prepare('SELECT p.id_publicacion, p.titulo, p.cantidad, p.direccion, p.estado, p.fecha_publicacion, m.nombre AS material 
                          FROM publicaciones p 
                          JOIN materiales m ON p.id_material = m.id_material 
                          WHERE p.id_usuario = ? 
                          ORDER BY p.fecha_publicacion DESC');
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$misPubs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Publicaciones - EcoConexión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <h1><a href="index.php">EcoConexión</a></h1>
        <nav>
            <a href="tablero.php">Tablero</a>
            <a href="publicar.php">Publicar Nuevo</a>
            <a href="auth/logout.php">Salir</a>
        </nav>
    </header>
    <div class="contenedor">
        <h2>Panel Administrativo de Mis Publicaciones</h2>
        <br>
        <?php if (count($misPubs) > 0): ?>
            <div class="grid-publicaciones">
                <?php foreach ($misPubs as $pub): ?>
                    <div class="pub-card <?= $pub['estado'] === 'recogido' ? 'recogido' : '' ?>">
                        <div>
                            <span class="badge"><?= e($pub['material']) ?></span>
                            <span class="badge" style="background: #eee; color: #333;"><?= strtoupper(e($pub['estado'])) ?></span>
                            <h3 style="margin: 0.5rem 0;"><?= e($pub['titulo']) ?></h3>
                            <p><strong>Cantidad:</strong> <?= e($pub['cantidad']) ?></p>
                            <p><strong>Dirección:</strong> <?= e($pub['direccion']) ?></p>
                        </div>
                        <div style="margin-top: 1rem;">
                            <?php if ($pub['estado'] === 'disponible'): ?>
                                <form action="mis_publicaciones.php" method="POST">
                                    <input type="hidden" name="id_publicacion" value="<?= $pub['id_publicacion'] ?>">
                                    <button type="submit" name="marcar_recogido" class="btn btn-secundario" style="width:100%;">Marcar como Recogido</button>
                                </form>
                            <?php else: ?>
                                <p style="color: #666; font-size: 0.9rem; text-align: center;">Material entregado/finalizado</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card"><p>Aún no has creado publicaciones de material. <a href="publicar.php">Haz clic aquí para publicar</a>.</p></div>
        <?php endif; ?>
    </div>
</body>
</html>