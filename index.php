<?php
declare(strict_types=1);
require_once __DIR__ . '/funciones/funciones.php';

$tituloPagina = 'Innovación en Reciclaje Urbano';
$clasePagina = 'app-bg';
require_once __DIR__ . '/includes/header.php';
?>

<div class="hero-section">
    <h1 class="hero-title">Conectamos residuos con quien los transforma</h1>
    <p class="hero-subtitle">
        Plataforma tecnológica avanzada para la gestión inteligente de materiales reciclables, uniendo generadores, recicladores y administradores comunitarios.
    </p>
    <?php if (estaAutenticado()): ?>
        <a href="tablero.php" class="btn" style="max-width: 260px; margin: 0 auto; background: white; color: var(--primary);">Explorar Tablero</a>
    <?php else: ?>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="auth/login.php" class="btn" style="max-width: 180px; background: white; color: var(--primary);">Iniciar Sesión</a>
            <a href="auth/registro.php" class="btn btn-secundario" style="max-width: 180px; background: rgba(255,255,255,0.2); border: 1px solid white;">Registrarse</a>
        </div>
    <?php endif; ?>
</div>

<div class="features-grid">
    <div class="feature-card">
        <h3>🌱 Para Generadores</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Publica tus materiales reciclables especificando cantidad, barrio y ciudad con geolocalización integrada para facilitar el retiro.</p>
    </div>
    <div class="feature-card">
        <h3>♻️ Para Recicladores</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Filtra rápidamente las ofertas disponibles por ubicación y contacta de inmediato con los generadores a través de canales directos.</p>
    </div>
    <div class="feature-card">
        <h3>🛡️ Control y Seguridad</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Moderación avanzada para administradores, control estricto de roles y autolimpieza de ofertas expiradas tras 6 horas de su recolección.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>