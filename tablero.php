<?php
declare(strict_types=1);
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

exigirAutenticacion();

$filtroMat = (int)($_GET['material'] ?? 0);

$resMat = $mysqli->query('SELECT id_material, nombre FROM materiales ORDER BY nombre ASC');
$categorias = $resMat->fetch_all(MYSQLI_ASSOC);

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

$tituloPagina = 'Tablero de Materiales';
require_once __DIR__ . '/includes/header.php';
?>

<h2>Materiales Disponibles para Reciclaje</h2>
<p style="color: var(--text-muted); margin-bottom: 1.5rem;">Consulta los residuos registrados y contacta al generador.</p>

<div class="filtros-bar">
    <a href="tablero.php" class="<?= $filtroMat === 0 ? 'activo' : '' ?>">Todos los materiales</a>
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
                    <h3 style="margin: 0.6rem 0; color: var(--primary);"><?= e($pub['titulo']) ?></h3>
                    <p><strong>Cantidad:</strong> <?= e($pub['cantidad']) ?></p>
                    <p><strong>Ubicación:</strong> <?= e($pub['direccion']) ?></p>
                </div>
                <div style="margin-top: 1.2rem;">
                    <a href="detalle.php?id=<?= $pub['id_publicacion'] ?>" class="btn">Ver Detalles de Entrega</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card"><p>No hay ofertas disponibles registradas en esta categoría.</p></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>