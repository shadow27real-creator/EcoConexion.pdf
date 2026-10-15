<?php
declare(strict_types=1);
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

exigirRol(['generador']);

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $id_material = (int)($_POST['id_material'] ?? 0);
    $cantidad = trim($_POST['cantidad'] ?? '');
    $ciudad = trim($_POST['ciudad'] ?? '');
    $barrio = trim($_POST['barrio'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (empty($titulo)) $errores[] = 'El título descriptivo es obligatorio.';
    if ($id_material <= 0) $errores[] = 'Seleccione una categoría válida.';
    if (empty($cantidad)) $errores[] = 'Debe especificar una cantidad válida.';
    if (empty($ciudad)) $errores[] = 'La ciudad es obligatoria.';
    if (empty($barrio)) $errores[] = 'El barrio es obligatorio.';
    if (mb_strlen($direccion) < 5) $errores[] = 'La dirección debe tener un mínimo de 5 caracteres.';

    if (count($errores) === 0) {
        $stmt = $mysqli->prepare('INSERT INTO publicaciones (titulo, cantidad, ciudad, barrio, direccion, descripcion, id_material, id_usuario, estado, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?, "disponible", NOW())');
        $stmt->bind_param('ssssssii', $titulo, $cantidad, $ciudad, $barrio, $direccion, $descripcion, $id_material, $_SESSION['usuario_id']);
        $stmt->execute();

        header('Location: mis_publicaciones.php');
        exit();
    }
}

$categorias = $mysqli->query('SELECT id_material, nombre FROM materiales ORDER BY nombre ASC')->fetch_all(MYSQLI_ASSOC);

$tituloPagina = 'Publicar Material';
require_once __DIR__ . '/includes/header.php';
?>

<div class="card card-center" style="max-width: 600px;">
    <h2>Nueva Oferta de Reciclaje</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Registre los residuos disponibles para recolección</p>

    <?php foreach ($errores as $err): ?>
        <div class="alerta alerta-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form action="publicar.php" method="POST">
        <div class="campo">
            <label for="titulo">Título de la Publicación:</label>
            <input type="text" id="titulo" name="titulo" value="<?= e($_POST['titulo'] ?? '') ?>" placeholder="Ej: Cartón limpio doblado y preparado" required>
        </div>
        <div class="campo">
            <label for="id_material">Categoría de Material:</label>
            <select id="id_material" name="id_material" required>
                <option value="">-- Seleccionar --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?= $cat['id_material'] ?>"><?= e($cat['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label for="cantidad">Cantidad Disponible:</label>
            <input type="text" id="cantidad" name="cantidad" value="<?= e($_POST['cantidad'] ?? '') ?>" placeholder="Ej: 10 kg / 3 cajas grandes" required>
        </div>
        <div style="display: flex; gap: 1rem;">
            <div class="campo" style="flex: 1;">
                <label for="ciudad">Ciudad:</label>
                <input type="text" id="ciudad" name="ciudad" value="<?= e($_POST['ciudad'] ?? '') ?>" placeholder="Ej: Bogotá" required>
            </div>
            <div class="campo" style="flex: 1;">
                <label for="barrio">Barrio:</label>
                <input type="text" id="barrio" name="barrio" value="<?= e($_POST['barrio'] ?? '') ?>" placeholder="Ej: Chapinero" required>
            </div>
        </div>
        <div class="campo">
            <label for="direccion">Dirección Exacta:</label>
            <input type="text" id="direccion" name="direccion" value="<?= e($_POST['direccion'] ?? '') ?>" placeholder="Ej: Carrera 15 # 45 - 10" required minlength="5">
        </div>
        <div class="campo">
            <label for="descripcion">Notas Adicionales (Opcional):</label>
            <textarea id="descripcion" name="descripcion" rows="3" placeholder="Detalles de disponibilidad horaria o de acceso..."></textarea>
        </div>
        <button type="submit" class="btn">Publicar Oferta</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>