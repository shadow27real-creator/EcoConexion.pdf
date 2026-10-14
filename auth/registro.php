<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../conexion/conexion.php';
require_once __DIR__ . '/../funciones/funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $tipo_usuario = trim($_POST['tipo_usuario'] ?? 'generador');

    if (mb_strlen($nombre) < 3) $errores[] = 'El nombre debe tener al menos 3 caracteres.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = 'Ingrese un correo electrónico válido.';
    if (strlen($password) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    if (empty($telefono)) $errores[] = 'El teléfono de contacto es obligatorio.';

    if (count($errores) === 0) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $mysqli->prepare('INSERT INTO usuarios (nombre, email, contrasena_hash, telefono, tipo_usuario) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $nombre, $email, $hash, $telefono, $tipo_usuario);
            $stmt->execute();

            $_SESSION['mensaje_exito'] = '¡Registro exitoso! Ahora puedes iniciar sesión.';
            header('Location: login.php');
            exit();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $errores[] = 'Este correo electrónico ya se encuentra registrado.';
            } else {
                $errores[] = 'Error al registrar el usuario en la base de datos.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - EcoConexión</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">EcoConexión</a></h1>
    </header>
    <div class="contenedor card" style="max-width: 500px;">
        <h2>Crear Cuenta</h2>
        <br>
        <?php foreach ($errores as $err): ?>
            <div class="alerta alerta-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form action="registro.php" method="POST">
            <div class="campo">
                <label>Nombre Completo:</label>
                <input type="text" name="nombre" value="<?= e($_POST['nombre'] ?? '') ?>" required>
            </div>
            <div class="campo">
                <label>Correo Electrónico:</label>
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="campo">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>
            <div class="campo">
                <label>Teléfono (WhatsApp):</label>
                <input type="text" name="telefono" value="<?= e($_POST['telefono'] ?? '') ?>" placeholder="Ej: 3001112233" required>
            </div>
            <div class="campo">
                <label>Tipo de Usuario:</label>
                <select name="tipo_usuario">
                    <option value="generador">Generador (Tengo material para entregar)</option>
                    <option value="reciclador">Reciclador (Busco material para recolectar)</option>
                </select>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Registrarme</button>
        </form>
        <p style="margin-top: 1rem; text-align: center;">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </div>
</body>
</html>