<?php
declare(strict_types=1);
require_once __DIR__ . '/funciones/funciones.php';

$tituloPagina = 'Conectamos residuos con quien los transforma';
require_once __DIR__ . '/includes/header.php';
?>

<div class="card" style="text-align: center; padding: 4rem 2rem;">
    <h1 style="font-size: 2.4rem; color: var(--primary); margin-bottom: 1rem;">Conectamos residuos con quien los transforma</h1>
    <p style="max-width: 600px; margin: 0 auto 2rem auto; color: var(--text-muted); font-size: 1.1rem;">
        EcoConexión es la plataforma comunitaria que facilita la recolección selectiva de materiales reciclables entre generadores y recicladores.
    </p>
    
    <?php if (estaAutenticado()): ?>
        <a href="tablero.php" class="btn" style="max-width: 280px; margin: 0 auto;">Ir al Tablero de Materiales</a>
    <?php else: ?>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="auth/login.php" class="btn" style="max-width: 200px;">Iniciar Sesión</a>
            <a href="auth/registro.php" class="btn btn-secundario" style="max-width: 200px;">Registrarse</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>