<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}

function estaAutenticado(): bool {
    return isset($_SESSION['usuario_id']);
}

// Control de Acceso: Redirige al login si no hay sesión activa
function exigirAutenticacion(): void {
    if (!estaAutenticado()) {
        header('Location: /EcoConexion/auth/login.php');
        exit();
    }
}

// Control de Roles Estricto según PDF
function tieneRol(array $rolesPermitidos): bool {
    if (!estaAutenticado()) return false;
    return in_array($_SESSION['usuario_tipo'] ?? '', $rolesPermitidos, true);
}

function exigirRol(array $rolesPermitidos): void {
    exigirAutenticacion();
    if (!tieneRol($rolesPermitidos)) {
        header('Location: /EcoConexion/tablero.php');
        exit();
    }
}

function limpiarTelefono(string $telefono): string {
    $limpio = preg_replace('/[^\d]/', '', $telefono);
    if (strlen($limpio) === 10 && strpos($limpio, '57') !== 0) {
        $limpio = '57' . $limpio;
    }
    return $limpio;
}