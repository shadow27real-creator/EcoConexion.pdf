<?php
declare(strict_types=1);
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

exigirRol(['administrador']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_publicacion'])) {
    $id_pub = (int)($_POST['id_publicacion'] ?? 0);
    if ($id_pub > 0) {
        $stmt = $mysqli->prepare('DELETE FROM publicaciones WHERE id_publicacion = ?');
        $stmt->bind_param('i', $id_pub);
        $stmt->execute();
        $_SESSION['mensaje_exito'] = 'La publicación ha sido eliminada por el administrador.';
        header('Location: admin.php');
        exit();
    }
}

// Paginación y Buscador para escalar a miles de registros
$busqueda = trim($_GET['q'] ?? '');
$pagina = max(1, (int)($_GET['p'] ?? 1));
$porPagina = 10;
$offset = ($pagina - 1) * $porPagina;

if (!empty($busqueda)) {
    $sqlCount = 'SELECT COUNT(*) as total FROM publicaciones p JOIN usuarios u ON p.id_usuario = u.id_usuario WHERE p.titulo LIKE ? OR u.nombre LIKE ?';
    $stmtC = $mysqli->prepare($sqlCount);
    $like = '%' . $busqueda . '%';
    $stmtC->bind_param('ss', $like, $like);
    $stmtC->execute();
    $totalPubs = $stmtC->get_result()->fetch_assoc()['total'];

    $sql = 'SELECT p.id_publicacion, p.titulo, p.cantidad, p.ciudad, p.estado, p.fecha_publicacion, 
                   m.nombre AS material, u.nombre AS autor, u.email AS autor_email
            FROM publicaciones p
            JOIN materiales m ON p.id_material = m.id_material
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            WHERE p.titulo LIKE ? OR u.nombre LIKE ?
            ORDER BY p.fecha_publicacion DESC LIMIT ? OFFSET ?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ssii', $like, $like, $porPagina, $offset);
    $stmt->execute();
    $publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $totalPubs = $mysqli->query('SELECT COUNT(*) as total FROM publicaciones')->fetch_assoc()['total'];

    $sql = 'SELECT p.id_publicacion, p.titulo, p.cantidad, p.ciudad, p.estado, p.fecha_publicacion, 
                   m.nombre AS material, u.nombre AS autor, u.email AS autor_email
            FROM publicaciones p
            JOIN materiales m ON p.id_material = m.id_material
            JOIN usuarios u ON p.id_usuario = u.id_usuario
            ORDER BY p.fecha_publicacion DESC LIMIT ? OFFSET ?';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $porPagina, $offset);
    $stmt->execute();
    $publicaciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$totalUsuarios = $mysqli->query('SELECT COUNT(*) as total FROM usuarios')->fetch_assoc()['total'];
$totalPaginas = ceil($totalPubs / $porPagina);

$tituloPagina = 'Panel de Administración';
require_once __DIR__ . '/includes/header.php';
?>

<h2>Panel de Control Administrativo</h2>
<p style="color: var(--text-muted); margin-bottom: 1.5rem;">Gestión optimizada y moderación escalable de contenidos.</p>

<?php if (isset($_SESSION['mensaje_exito'])): ?>
    <div class="alerta alerta-exito"><?= e($_SESSION['mensaje_exito']) ?></div>
    <?php unset($_SESSION['mensaje_exito']); ?>
<?php endif; ?>

<div style="display: flex; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="flex: 1; text-align: center; padding: 1.2rem;">
        <h3 style="font-size: 2rem; color: var(--primary);"><?= $totalPubs ?></h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Publicaciones Totales</p>
    </div>
    <div class="card" style="flex: 1; text-align: center; padding: 1.2rem;">
        <h3 style="font-size: 2rem; color: var(--primary);"><?= $totalUsuarios ?></h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Usuarios Registrados</p>
    </div>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <h3>Listado General y Moderación</h3>
    <form action="admin.php" method="GET" style="display: flex; gap: 0.5rem;">
        <input type="text" name="q" value="<?= e($busqueda) ?>" placeholder="Buscar por título o autor..." style="padding: 0.5rem; border: 1px solid var(--border); border-radius: 6px; width: 240px;">
        <button type="submit" class="btn" style="width: auto; padding: 0.5rem 1rem;">Buscar</button>
    </form>
</div>

<?php if (count($publicaciones) > 0): ?>
    <div class="card" style="padding: 1rem; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border); background: var(--bg);">
                    <th style="padding: 0.8rem;">ID</th>
                    <th style="padding: 0.8rem;">Título</th>
                    <th style="padding: 0.8rem;">Material</th>
                    <th style="padding: 0.8rem;">Autor / Ciudad</th>
                    <th style="padding: 0.8rem;">Estado</th>
                    <th style="padding: 0.8rem;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($publicaciones as $pub): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 0.8rem;"><?= $pub['id_publicacion'] ?></td>
                        <td style="padding: 0.8rem;"><strong><?= e($pub['titulo']) ?></strong></td>
                        <td style="padding: 0.8rem;"><span class="badge"><?= e($pub['material']) ?></span></td>
                        <td style="padding: 0.8rem;"><?= e($pub['autor']) ?><br><small style="color: var(--text-muted);"><?= e($pub['ciudad']) ?></small></td>
                        <td style="padding: 0.8rem;">
                            <span class="badge" style="background: <?= $pub['estado'] === 'disponible' ? '#d8f3dc' : '#f8d7da' ?>; color: var(--text);">
                                <?= strtoupper(e($pub['estado'])) ?>
                            </span>
                        </td>
                        <td style="padding: 0.8rem;">
                            <form action="admin.php" method="POST" onsubmit="return confirm('¿Está seguro de eliminar esta publicación?');">
                                <input type="hidden" name="id_publicacion" value="<?= $pub['id_publicacion'] ?>">
                                <button type="submit" name="eliminar_publicacion" class="btn" style="background: var(--danger); padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($totalPaginas > 1): ?>
        <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 1.5rem;">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="admin.php?p=<?= $i ?><?= !empty($busqueda) ? '&q=' . urlencode($busqueda) : '' ?>" 
                   class="btn" style="width: auto; padding: 0.4rem 0.8rem; background: <?= $pagina === $i ? 'var(--primary)' : 'var(--text-muted)' ?>;">
                   <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="card"><p>No se encontraron publicaciones en el sistema.</p></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>