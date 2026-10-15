<?php
declare(strict_types=1);
require_once __DIR__ . '/funciones/funciones.php';

$tituloPagina = 'Inicio - Plataforma de Reciclaje Comunitaria';
$clasePagina = 'app-bg';
require_once __DIR__ . '/includes/header.php';
?>

<!-- BANNER HERO -->
<section class="hero-banner">
    <h1>Transforma tus residuos en nuevas oportunidades</h1>
    <p>
        EcoConexión es la solución tecnológica que une generadores de residuos reciclables con recicladores y centros de acopio en Colombia.
    </p>
    <div class="hero-actions">
        <?php if (estaAutenticado()): ?>
            <a href="tablero.php" class="btn-hero btn-hero-primary">Explorar Tablero de Materiales</a>
        <?php else: ?>
            <a href="auth/registro.php" class="btn-hero btn-hero-primary">Unirse Gratis</a>
            <a href="auth/login.php" class="btn-hero btn-hero-secondary">Iniciar Sesión</a>
        <?php endif; ?>
    </div>
</section>

<!-- SECCIÓN INFORMATIVA / BENEFICIOS -->
<section class="cards-grid-index">
    <article class="card-index">
        <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=600&q=80" alt="Generadores de residuos">
        <div class="card-index-content">
            <h3>Para Generadores</h3>
            <p>Publica fácilmente los residuos de tu hogar, conjunto o empresa (cartón, plástico, vidrio, metal) indicando ubicación con mapa interactivo.</p>
        </div>
    </article>

    <article class="card-index">
        <img src="https://images.unsplash.com/photo-1595278069441-2cf29f8005a4?auto=format&fit=crop&w=600&q=80" alt="Recicladores">
        <div class="card-index-content">
            <h3>Para Recicladores</h3>
            <p>Encuentra de forma ágil ofertas disponibles cerca de ti, filtrando por ciudad o barrio y coordinando directamente por WhatsApp.</p>
        </div>
    </article>

    <article class="card-index">
        <img src="https://images.unsplash.com/photo-1611284446314-60a58ac0deb9?auto=format&fit=crop&w=600&q=80" alt="Sostenibilidad">
        <div class="card-index-content">
            <h3>Economía Circular</h3>
            <p>Fomentamos la inclusión social y reducimos la huella de carbono, canalizando toneladas de residuos directamente hacia la industria transformadora.</p>
        </div>
    </article>
</section>

<!-- TUTORIAL GUÍA: ¿CÓMO VER Y USAR LAS PUBLICACIONES? -->
<section class="tutorial-section">
    <h2 class="tutorial-title">¿Cómo consultar y utilizar las publicaciones?</h2>
    <p class="tutorial-subtitle">Guía rápida de 4 pasos para interactuar con la plataforma</p>

    <div class="tutorial-steps">
        <div class="step-card">
            <div class="step-number">1</div>
            <h4>Crea tu Cuenta o Ingresa</h4>
            <p>Regístrate como <strong>Generador</strong> (si entregas residuos) o <strong>Reciclador</strong> (si recolectas materiales) en pocos segundos.</p>
        </div>

        <div class="step-card">
            <div class="step-number">2</div>
            <h4>Entra al Tablero</h4>
            <p>Haz clic en <strong>Tablero</strong> en el menú superior para acceder al listado actualizado en tiempo real de todos los residuos disponibles.</p>
        </div>

        <div class="step-card">
            <div class="step-number">3</div>
            <h4>Filtra por Ciudad o Barrio</h4>
            <p>Selecciona la categoría (cartón, vidrio, etc.) y escribe tu ciudad o barrio en el buscador dinámico para encontrar las ofertas más cercanas.</p>
        </div>

        <div class="step-card">
            <div class="step-number">4</div>
            <h4>Ver Mapa y Contactar</h4>
            <p>Abre el detalle para ver la dirección en un <strong>Google Map interactivo</strong> y presiona el botón para acordar el retiro directamente vía WhatsApp.</p>
        </div>
    </div>
</section>

<!-- ESTADÍSTICAS -->
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