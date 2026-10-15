<?php
declare(strict_types=1);
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

exigirAutenticacion();

// REQUISITO: Ocultar automáticamente las publicaciones marcadas como "recogido" tras 6 horas
$mysqli->query('DELETE FROM publicaciones WHERE estado = "recogido" AND fecha_actualizacion < (NOW() - INTERVAL 6 HOUR)');

$filtroMat = (int)($_GET['material'] ?? 0);
$filtroCiudad = trim($_GET['ciudad'] ?? '');
$filtroBarrio = trim($_GET['barrio'] ?? '');

$resMat = $mysqli->query('SELECT id_material, nombre FROM materiales ORDER BY nombre ASC');
$categorias = $resMat->fetch_all(MYSQLI_ASSOC);

$resCiudades = $mysqli->query('SELECT DISTINCT ciudad FROM publicaciones WHERE ciudad IS NOT NULL AND ciudad != "" ORDER BY ciudad ASC');
$ciudades = $resCiudades->fetch_all(MYSQLI_ASSOC);

$sql = 'SELECT p.id_publicacion, p.titulo, p.cantidad, p.direccion, p.barrio, p.ciudad, p.estado, p.fecha_publicacion, m.nombre AS material
        FROM publicaciones p
        JOIN materiales m ON p.id_material = m.id_material
        WHERE p.estado = "disponible"';

$params = [];
$types = '';

if ($filtroMat > 0) {
    $sql .= ' AND p.id_material = ?';
    $params[] = $filtroMat;
    $types .= 'i';
}
if (!empty($filtroCiudad)) {
    $sql .= ' AND p.ciudad = ?';
    $params[] = $filtroCiudad;
    $types .= 's';
}
if (!empty($filtroBarrio)) {
    $sql .= ' AND p.barrio LIKE ?';
    $params[] = '%' . $filtroBarrio . '%';
    $types .= 's';
}

$sql .= ' ORDER BY p.fecha_publicacion DESC';

if (count($params) > 0) {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $publicaciones = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);
}

$tituloPagina = 'Tablero de Materiales';
$clasePagina = 'app-bg';
require_once __DIR__ . '/includes/header.php';
?>

<h2>Materiales Disponibles para Reciclaje</h2>
<p style="color: var(--text-muted); margin-bottom: 1.5rem;">Filtra los residuos por categoría, ciudad y barrio.</p>

<form action="tablero.php" method="GET" class="filtros-box">
    <div class="campo">
        <label for="material">Categoría:</label>
        <select id="material" name="material">
            <option value="0">Todos los materiales</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id_material'] ?>" <?= $filtroMat === (int)$cat['id_material'] ? 'selected' : '' ?>>
                    <?= e($cat['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="campo">
        <label for="ciudad">Ciudad:</label>
        <select id="ciudad" name="ciudad">
            <option value="">Todas las ciudades</option>
            <?php foreach ($ciudades as $c): ?>
                <option value="<?= e($c['ciudad']) ?>" <?= $filtroCiudad === $c['ciudad'] ? 'selected' : '' ?>>
                    <?= e($c['ciudad']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="campo">
        <label for="barrio">Barrio:</label>
        <input type="text" id="barrio" name="barrio" value="<?= e($filtroBarrio) ?>" placeholder="Ej: Centro, El Prado...">
    </div>
    <div class="campo" style="flex: 0; min-width: auto;">
        <button type="submit" class="btn" style="padding: 0.65rem 1.2rem;">Filtrar</button>
    </div>
</form>

<?php if (count($publicaciones) > 0): ?>
    <div class="grid-publicaciones">
        <?php foreach ($publicaciones as $pub): ?>
            <div class="pub-card">
                <div>
                    <span class="badge"><?= e($pub['material']) ?></span>
                    <h3 style="margin: 0.6rem 0; color: var(--primary);"><?= e($pub['titulo']) ?></h3>
                    <p><strong>Cantidad:</strong> <?= e($pub['cantidad']) ?></p>
                    <p><strong>Ubicación:</strong> <?= e($pub['barrio']) ?>, <?= e($pub['ciudad']) ?></p>
                </div>
                <div style="margin-top: 1.2rem;">
                    <a href="detalle.php?id=<?= $pub['id_publicacion'] ?>" class="btn">Ver Mapa y Detalles</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card"><p>No hay ofertas disponibles con los filtros seleccionados.</p></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>