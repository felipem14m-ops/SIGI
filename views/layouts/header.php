<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Versión del sistema para cache busting de assets CSS/JS
define('SIGI_VERSION', '2.1.0');

$base_url = '/';
if (strpos($_SERVER['REQUEST_URI'], '/SIGI/') === 0) {
    $base_url = '/SIGI/';
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: " . $base_url . "views/Usuario/login.php");
    exit;
}

$usuario = $_SESSION['usuario'] ?? [];
$titulo = $titulo ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo) ?> - SIGI</title>
    <link rel="shortcut icon" type="image/png" href="<?= $base_url ?>IMG/LOGO SIGI.png">

    <!-- =============================================
   ESTILOS EXTERNOS - ARQUITECTURA CSS LIMPIA
   ============================================= -->

    <!-- Google Fonts - Tipografías para todo el sistema -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Bebas+Neue&family=Oswald:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS - Framework base para componentes y grid -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome - Iconos para todo el sistema -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- =============================================
   ESTILOS PERSONALIZADOS SIGI - NUEVA ARQUITECTURA
   ============================================= -->

    <!-- estilos-globales.css - Estilos del sistema (login, dashboard, registro, welcome) -->
    <link rel="stylesheet" href="<?= $base_url ?>CSS/estilos-globales.css?v=<?= SIGI_VERSION ?>">

    <!-- estilos-paginas.css - Estilos específicos de páginas (index, landing, footer) -->
    <link rel="stylesheet" href="<?= $base_url ?>CSS/estilos-paginas.css?v=<?= SIGI_VERSION ?>">

    <!-- sidebar.css - Estilos de la barra lateral y topbar del panel -->
    <link rel="stylesheet" href="<?= $base_url ?>CSS/sidebar.css?v=<?= SIGI_VERSION ?>">

    <!-- admin.css - Estilos del dashboard de administración -->
    <link rel="stylesheet" href="<?= $base_url ?>CSS/admin.css?v=<?= SIGI_VERSION ?>">

    <!-- DataTables CSS - Tablas interactivas profesionales -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
</head>

<body class="bg-light">
    <div class="layout-dashboard">