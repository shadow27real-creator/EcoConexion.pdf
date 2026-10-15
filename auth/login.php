<?php
declare(strict_types=1);
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
        $error = 'Las credenciales ingresadas son incorrectas.';
    }
}

$tituloPagina = 'Iniciar Sesión';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card card-center">
    <h2>Iniciar Sesión</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Ingresa a tu cuenta de EcoConexión</p>

    <?php if (isset($_SESSION['mensaje_exito'])): ?>
        <div class="alerta alerta-exito"><?= e($_SESSION['mensaje_exito']) ?></div>
        <?php unset($_SESSION['mensaje_exito']); ?>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alerta alerta-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="campo">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="campo">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Ingresar al Sistema</button>
    </form>
    <p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
        ¿No tienes cuenta? <a href="registro.php" style="color: var(--primary); font-weight: 600;">Regístrate aquí</a>
    </p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>