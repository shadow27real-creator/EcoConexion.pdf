<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/conexion/conexion.php';
require_once __DIR__ . '/funciones/funciones.php';

if (!estaAutenticado()) {
    header('Location: auth/login.php');
    exit();
}

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $id_material = (int)($_POST['id_material'] ?? 0);
    $cantidad = trim($_POST['cantidad'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if (empty($titulo)) $errores[] = 'El título de la publicación es obligatorio.';
    if ($id_material <= 0) $errores[] = 'Seleccione una categoría de material.';
    if (empty($cantidad)) $errores[] = 'Especifique la cantidad disponible.';
    if (empty($direccion)) $errores[] = 'La dirección escrita es requerida para el retiro.';

    if (count($errores) === 0) {
        $stmt = $mysqli->prepare('INSERT INTO publicaciones (titulo, cantidad, direccion, descripcion, id_material, id_usuario) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssii', $titulo, $cantidad, $direccion, $descripcion, $id_material, $_SESSION['usuario_id']);
        $stmt->execute();

        header('Location: mis_publicaciones.php');
        exit();
    }
}

$categorias = $mysqli->query('SELECT id_material, nombre FROM materiales ORDER BY nombre ASC')->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Material - EcoConexión</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <header>
        <h1><a href="index.php">EcoConexión</a></h1>
        <nav>
            <a href="tablero.php">Tablero</a>
            <a href="mis_publicaciones.php">Mis Publicaciones</a>
            <a href="auth/logout.php">Salir</a>
        </nav>
    </header>
    <div class="contenedor card" style="max-width: 600px;">
        <h2>Nueva Publicación de Reciclaje</h2>
        <br>
        <?php foreach ($errores as $err): ?>
            <div class="alerta alerta-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form action="publicar.php" method="POST">
            <div class="campo">
                <label>Título Informativo:</label>
                <input type="text" name="titulo" placeholder="Ej: Cartón corrugado y cajas varias" required>
            </div>
            <div class="campo">
                <label>Categoría de Material:</label>
                <select name="id_material" required>
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id_material'] ?>"><?= e($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label>Cantidad Estimada:</label>
                <input type="text" name="cantidad" placeholder="Ej: 15 kg / 3 bolsas grandes" required>
            </div>
            <div class="campo">
                <label>Dirección de Recogida (Texto):</label>
                <input type="text" name="direccion" placeholder="Ej: Calle 10 # 5 - 20, Barrio Centro" required>
            </div>
            <div class="campo">
                <label>Descripción Adicional (Opcional):</label>
                <textarea name="descripcion" rows="3" placeholder="Detalles sobre empaque, disponibilidad de horario, etc."></textarea>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Publicar Oferta</button>
        </form>
    </div>
</body>
</html>