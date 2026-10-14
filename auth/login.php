<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../conexion/conexion.php';
require_once __DIR__ . '/../funciones/funciones.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $mysqli->prepare('SELECT id_usuario, nombre, contrasena_hash, tipo_usuario FROM usuarios WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $usuario = $stmt->get_result()->fetch_assoc();

    if ($usuario && password_verify($password, $usuario['contrasena_hash'])) {
        $_SESSION['usuario_id'] = $usuario['id_usuario'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_tipo'] = $usuario['tipo_usuario'];

        header('Location: ../tablero.php');
        exit();
    } else {
        $error = 'Credenciales incorrectas. Verifique su correo y contraseña.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - EcoConexión</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <header>
        <h1><a href="../index.php">EcoConexión</a></h1>
    </header>
    <div class="contenedor card" style="max-width: 450px;">
        <h2>Iniciar Sesión</h2>
        <br>
        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alerta alerta-exito"><?= e($_SESSION['mensaje_exito']) ?></div>
            <?php unset($_SESSION['mensaje_exito']); ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alerta alerta-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="campo">
                <label>Correo Electrónico:</label>
                <input type="email" name="email" required>
            </div>
            <div class="campo">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Ingresar</button>
        </form>
        <p style="margin-top: 1rem; text-align: center;">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>