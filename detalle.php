<?php
declare(strict_types=1);
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

exigirAutenticacion();

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
$mensajeWs = urlencode("Hola " . $pub['generador'] . ", vi tu oferta de '" . $pub['titulo'] . "' en EcoConexión y deseo acordar el retiro del material.");
$urlWhatsapp = "https://api.whatsapp.com/send?phone=" . $telLimpio . "&text=" . $mensajeWs;

$tituloPagina = $pub['titulo'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="card card-center" style="max-width: 650px;">
    <span class="badge"><?= e($pub['material']) ?></span>
    <h2 style="color: var(--primary); margin: 0.6rem 0;"><?= e($pub['titulo']) ?></h2>
    <p style="font-size: 0.9rem;"><strong>Estado de disponibilidad:</strong> <?= strtoupper(e($pub['estado'])) ?></p>
    <hr style="margin: 1rem 0; border: 0; border-top: 1px solid var(--border);">

    <p><strong>Ofrecido por:</strong> <?= e($pub['generador']) ?></p>
    <p><strong>Cantidad informada:</strong> <?= e($pub['cantidad']) ?></p>
    <p><strong>Dirección de entrega:</strong> <?= e($pub['direccion']) ?></p>
    <br>
    <p><strong>Descripción:</strong></p>
    <p style="color: var(--text-muted);"><?= !empty($pub['descripcion']) ? nl2br(e($pub['descripcion'])) : 'Sin especificaciones adicionales.' ?></p>
    <br>

    <?php if ($pub['estado'] === 'disponible'): ?>
        <a href="<?= $urlWhatsapp ?>" target="_blank" rel="noopener noreferrer" class="btn btn-ws">
            Contactar por WhatsApp
        </a>
    <?php else: ?>
        <div class="alerta alerta-error" style="text-align: center;">Este material ya ha sido recogido.</div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>