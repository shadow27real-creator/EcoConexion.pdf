<?php
declare(strict_types=1);
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

exigirRol(['generador']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_recogido'])) {
    $id_pub = (int)($_POST['id_publicacion'] ?? 0);
    
    $stmt = $mysqli->prepare('UPDATE publicaciones SET estado = "recogido", fecha_actualizacion = NOW() WHERE id_publicacion = ? AND id_usuario = ?');
    $stmt->bind_param('ii', $id_pub, $_SESSION['usuario_id']);
    $stmt->execute();

    header('Location: mis_publicaciones.php');
    exit();
}

$stmt = $mysqli->prepare('SELECT p.id_publicacion, p.titulo, p.cantidad, p.direccion, p.barrio, p.ciudad, p.estado, p.fecha_publicacion, m.nombre AS material 
                          FROM publicaciones p 
                          JOIN materiales m ON p.id_material = m.id_material 
                          WHERE p.id_usuario = ? 
                          ORDER BY p.fecha_publicacion DESC');
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$misPubs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$tituloPagina = 'Mis Ofertas';
require_once __DIR__ . '/includes/header.php';
?>

<h2>Mis Ofertas de Reciclaje</h2>
<p style="color: var(--text-muted); margin-bottom: 1.5rem;">Gestione el estado de sus publicaciones (las recogidas se eliminarán automáticamente tras 6 horas)</p>

<?php if (count($misPubs) > 0): ?>
    <div class="grid-publicaciones">
        <?php foreach ($misPubs as $pub): ?>
            <div class="pub-card <?= $pub['estado'] === 'recogido' ? 'recogido' : '' ?>">
                <div>
                    <span class="badge"><?= e($pub['material']) ?></span>
                    <span class="badge" style="background: #e9ecef; color: var(--text);"><?= strtoupper(e($pub['estado'])) ?></span>
                    <h3 style="margin: 0.6rem 0;"><?= e($pub['titulo']) ?></h3>
                    <p><strong>Cantidad:</strong> <?= e($pub['cantidad']) ?></p>
                    <p><strong>Ubicación:</strong> <?= e($pub['barrio']) ?>, <?= e($pub['ciudad']) ?></p>
                </div>
                <div style="margin-top: 1.2rem;">
                    <?php if ($pub['estado'] === 'disponible'): ?>
                        <form action="mis_publicaciones.php" method="POST">
                            <input type="hidden" name="id_publicacion" value="<?= $pub['id_publicacion'] ?>">
                            <button type="submit" name="marcar_recogido" class="btn btn-secundario">Marcar como Recogido</button>
                        </form>
                    <?php else: ?>
                        <p style="color: var(--text-muted); font-size: 0.85rem; text-align: center;">Recogido (Expirará en < 6h)</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card"><p>No ha publicado ofertas aún. <a href="publicar.php" style="color: var(--primary);">Crear una nueva oferta</a>.</p></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>