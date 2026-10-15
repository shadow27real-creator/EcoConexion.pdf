<?php
declare(strict_types=1);
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

exigirAutenticacion();

// Ocultar/eliminar automáticamente publicaciones recogidas pasadas 6 horas
$mysqli->query('DELETE FROM publicaciones WHERE estado = "recogido" AND fecha_actualizacion < (NOW() - INTERVAL 6 HOUR)');

$filtroMat = (int)($_GET['material'] ?? 0);
$filtroCiudad = trim($_GET['ciudad'] ?? '');
$filtroTexto = trim($_GET['q'] ?? '');

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
if (!empty($filtroTexto)) {
    $sql .= ' AND (p.titulo LIKE ? OR p.barrio LIKE ? OR p.direccion LIKE ?)';
    $like = '%' . $filtroTexto . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
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

<h2 style="color: var(--primary); font-weight: 800; margin-bottom: 0.3rem;">Tablero de Materiales Reciclables</h2>
<p style="color: var(--text-muted); margin-bottom: 1.5rem;">Filtra rápidamente por categoría, ciudad o barrio.</p>

<div class="filter-dashboard">
    <div class="filter-dashboard-header">🔍 Categorías de Material:</div>

    <div class="chips-container">
        <a href="tablero.php?material=0&ciudad=<?= urlencode($filtroCiudad) ?>&q=<?= urlencode($filtroTexto) ?>" 
           class="chip <?= $filtroMat === 0 ? 'active' : '' ?>">
           Todos
        </a>
        <?php foreach ($categorias as $cat): ?>
            <a href="tablero.php?material=<?= $cat['id_material'] ?>&ciudad=<?= urlencode($filtroCiudad) ?>&q=<?= urlencode($filtroTexto) ?>" 
               class="chip <?= $filtroMat === (int)$cat['id_material'] ? 'active' : '' ?>">
               <?= e($cat['nombre']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <form action="tablero.php" method="GET" class="filter-inputs-grid">
        <input type="hidden" name="material" value="<?= $filtroMat ?>">
        
        <div class="campo">
            <label for="ciudad">Filtrar por Ciudad:</label>
            <select id="ciudad" name="ciudad">
                <option value="">Todas las Ciudades</option>
                <?php foreach ($ciudades as $c): ?>
                    <option value="<?= e($c['ciudad']) ?>" <?= $filtroCiudad === $c['ciudad'] ? 'selected' : '' ?>>
                        <?= e($c['ciudad']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="q">Barrio o Descripción:</label>
            <input type="text" id="q" name="q" value="<?= e($filtroTexto) ?>" placeholder="Ej: Chapinero, Centro, Cartón...">
        </div>

        <div class="campo" style="flex: 0;">
            <button type="submit" class="btn" style="padding: 0.65rem 1.5rem;">Buscar</button>
        </div>
    </form>
</div>

<?php if (count($publicaciones) > 0): ?>
    <div class="grid-publicaciones">
        <?php foreach ($publicaciones as $pub): ?>
            <div class="pub-card">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span class="badge"><?= e($pub['material']) ?></span>
                        <small style="color: var(--text-muted); font-size: 0.78rem;"><?= e(date('d/m/Y', strtotime($pub['fecha_publicacion']))) ?></small>
                    </div>
                    <h3 style="margin: 0.4rem 0 0.8rem 0; color: var(--primary); font-size: 1.15rem;"><?= e($pub['titulo']) ?></h3>
                    <p><strong>Cantidad:</strong> <?= e($pub['cantidad']) ?></p>
                    <p><strong>Ubicación:</strong> <?= e($pub['barrio']) ?>, <?= e($pub['ciudad']) ?></p>
                </div>
                <div style="margin-top: 1.2rem;">
                    <a href="detalle.php?id=<?= $pub['id_publicacion'] ?>" class="btn">Ver Mapa y Contacto</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card" style="text-align: center; padding: 3rem 1rem;">
        <p style="font-size: 1.1rem; color: var(--text-muted);">No se encontraron ofertas con los filtros ingresados.</p>
        <a href="tablero.php" style="color: var(--primary); font-weight: 700; margin-top: 0.5rem; display: inline-block;">Limpiar filtros de búsqueda</a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>