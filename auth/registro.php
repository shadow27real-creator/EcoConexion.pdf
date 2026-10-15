<?php
declare(strict_types=1);
require_once __DIR__ . '/../conexion/conexion.php';
require_once __DIR__ . '/../funciones/funciones.php';

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $telefono = trim($_POST['telefono'] ?? '');
    $tipo_usuario = trim($_POST['tipo_usuario'] ?? '');

    // Validaciones estrictas según PDF Sección 7
    if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre debe tener entre 2 y 100 caracteres.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Ingrese un correo electrónico con formato válido.';
    }
    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener un mínimo de 8 caracteres.';
    }
    if (!preg_match('/^[0-9]{7,15}$/', preg_replace('/[^\d]/', '', $telefono))) {
        $errores[] = 'El teléfono debe contener únicamente números (entre 7 y 15 dígitos).';
    }
    
    // Regla de Negocio: Validar únicamente roles permitidos (PDF Sección 7)
    $rolesPermitidos = ['generador', 'reciclador'];
    if (!in_array($tipo_usuario, $rolesPermitidos, true)) {
        $errores[] = 'El tipo de usuario seleccionado no es válido.';
    }

    if (count($errores) === 0) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $mysqli->prepare('INSERT INTO usuarios (nombre, email, contrasena_hash, telefono, tipo_usuario) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $nombre, $email, $hash, $telefono, $tipo_usuario);
            $stmt->execute();

            $_SESSION['mensaje_exito'] = 'Registro completado. Por favor inicie sesión.';
            header('Location: login.php');
            exit();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $errores[] = 'Este correo electrónico ya se encuentra registrado.';
            } else {
                $errores[] = 'Error al registrar la cuenta en el sistema.';
            }
        }
    }
}

$tituloPagina = 'Registro de Usuario';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="card card-center">
    <h2>Crear Cuenta</h2>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem; font-size: 0.9rem;">Únete a la red comunitaria de reciclaje</p>

    <?php foreach ($errores as $err): ?>
        <div class="alerta alerta-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form action="registro.php" method="POST">
        <div class="campo">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" value="<?= e($_POST['nombre'] ?? '') ?>" required minlength="2" maxlength="100">
        </div>
        <div class="campo">
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
        </div>
        <div class="campo">
            <label for="password">Contraseña (Mínimo 8 caracteres):</label>
            <input type="password" id="password" name="password" required minlength="8">
        </div>
        <div class="campo">
            <label for="telefono">Teléfono de Contacto (WhatsApp):</label>
            <input type="tel" id="telefono" name="telefono" value="<?= e($_POST['telefono'] ?? '') ?>" placeholder="Ej: 3001112233" required>
        </div>
        <div class="campo">
            <label for="tipo_usuario">Tipo de Usuario:</label>
            <select id="tipo_usuario" name="tipo_usuario" required>
                <option value="generador">Generador (Tengo residuos para entregar)</option>
                <option value="reciclador">Reciclador (Busco residuos para recolectar)</option>
            </select>
        </div>
        <button type="submit" class="btn">Completar Registro</button>
    </form>
    <p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
        ¿Ya tienes cuenta? <a href="login.php" style="color: var(--primary); font-weight: 600;">Inicia Sesión</a>
    </p>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>