<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

$id_pub = (int)($_GET['id'] ?? 0);

$stmt = $mysqli->prepare('SELECT p.id_publicacion, p.titulo, p.cantidad, p.direccion, p.descripcion, p.estado, p.fecha_publicacion, m.nombre AS material, u.nombre AS generador, u.telefono 
                          FROM publicaciones p 
                          JOIN materiales m ON p.id_material = m.id_material 
                          JOIN usuarios u ON p.id_usuario = u.id_usuario 
                          WHERE p.id_publicacion = ?');
$stmt->bind_param('i', $id_pub);
$stmt->execute();
$pub = $stmt->get_result()->fetch_assoc();

if (!$pub) {
    http_response_code(404);
    exit('Publicación no encontrada.');
}

$telLimpio = limpiarTelefono($pub['telefono']);
$mensajeWs = urlencode("Hola " . $pub['generador'] . ", vi tu publicación '" . $pub['titulo'] . "' en EcoConexión y estoy interesado en retirar el material.");
$urlWhatsapp = "https://api.whatsapp.com/send?phone=" . $telLimpio . "&text=" . $mensajeWs;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= e($pub['titulo']) ?> - EcoConexión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <h1><a href="index.php">EcoConexión</a></h1>
        <nav>
            <a href="tablero.php">Volver al Tablero</a>
        </nav>
    </header>
    <div class="contenedor card" style="max-width: 700px;">
        <span class="badge"><?= e($pub['material']) ?></span>
        <h2 style="color: #2e7d32; margin: 0.5rem 0;"><?= e($pub['titulo']) ?></h2>
        <p><strong>Estado:</strong> <?= strtoupper(e($pub['estado'])) ?></p>
        <hr style="margin: 1rem 0; border: 0; border-top: 1px solid #eee;">
        
        <p><strong>Publicado por:</strong> <?= e($pub['generador']) ?></p>
        <p><strong>Cantidad disponible:</strong> <?= e($pub['cantidad']) ?></p>
        <p><strong>Dirección de entrega:</strong> <?= e($pub['direccion']) ?></p>
        <br>
        <p><strong>Detalles adicionales:</strong></p>
        <p><?= !empty($pub['descripcion']) ? nl2br(e($pub['descripcion'])) : 'Sin especificaciones adicionales.' ?></p>
        <br>

        <?php if ($pub['estado'] === 'disponible'): ?>
            <a href="<?= $urlWhatsapp ?>" target="_blank" class="btn btn-ws" style="display: block; text-align: center; font-size: 1.1rem;">
                📱 Contactar por WhatsApp
            </a>
        <?php else: ?>
            <div class="alerta alerta-error" style="text-align: center;">Este material ya ha sido marcado como recogido.</div>
        <?php endif; ?>
    </div>
</body>
</html>