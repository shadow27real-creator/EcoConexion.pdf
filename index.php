<?php
declare(strict_types=1);
require_once __DIR__ . '/funciones/funciones.php';

$tituloPagina = 'Inicio - Plataforma de Reciclaje';
$clasePagina = 'app-bg';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero-banner">
    <h1>Transforma tus residuos en nuevas oportunidades</h1>
    <p>
        EcoConexión es la solución digital que conecta generadores de residuos con recicladores y centros de acopio en Colombia.
    </p>
    <div class="hero-actions">
        <?php if (estaAutenticado()): ?>
            <a href="tablero.php" class="btn-hero btn-hero-primary">Explorar Tablero</a>
        <?php else: ?>
            <a href="auth/registro.php" class="btn-hero btn-hero-primary">Unirse Gratis</a>
            <a href="auth/login.php" class="btn-hero btn-hero-secondary">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</section>

<section class="cards-grid-index">
    <article class="card-index">
        <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=600&q=80" alt="Generadores de residuos">
        <div class="card-index-content">
            <h3>Para Generadores</h3>
            <p>Publica materiales reciclables de tu hogar o empresa detallando la ubicación exacta con mapa interactivo.</p>
        </div>
    </article>

    <article class="card-index">
        <img src="https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?auto=format&fit=crop&w=600&q=80" alt="Recicladores">
        <div class="card-index-content">
            <h3>Para Recicladores</h3>
            <p>Filtra fácilmente las ofertas disponibles por ciudad y barrio, contactando directamente al entregador vía WhatsApp.</p>
        </div>
    </article>

    <article class="card-index">
        <img src="https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?auto=format&fit=crop&w=600&q=80" alt="Gestión Sostenible">
        <div class="card-index-content">
            <h3>Impacto Social</h3>
            <p>Moderación activa y autolimpieza de ofertas recogidas para asegurar información confiable y limpia en todo momento.</p>
        </div>
    </article>
</section>

<section class="stats-banner">
    <div class="stat-item">
        <h4>100%</h4>
        <p>Inclusivo y Gratuito</p>
    </div>
    <div class="stat-item">
        <h4>+6</h4>
        <p>Categorías de Materiales</p>
    </div>
    <div class="stat-item">
        <h4>6 Horas</h4>
        <p>Autolimpieza de Publicaciones Recogidas</p>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>