<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGI - Sistema de Gestión de Inventario</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Landing Page Styles -->
    <link rel="stylesheet" href="../CSS/index.css?v=<?php echo time(); ?>">
    <link rel="shortcut icon" type="image/png" href="../IMG/LOGO SIGI.png">

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <div class="nav-brand-icon"><i class="fas fa-store"></i></div>
            SIGI
        </a>

        <div class="nav-links">
            <a href="#inicio" class="nav-link">Inicio</a>
            <a href="#caracteristicas" class="nav-link">Características</a>
            <a href="#nosotros" class="nav-link">Nosotros</a>
        </div>

        <div class="nav-actions">
            <div class="nav-divider"></div>
            <a href="../views/Usuario/login.php" class="btn-primary">
                Ingresar <i class="fas fa-arrow-right" style="font-size: 0.8rem;"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="inicio">
        <div class="hero-content">
            <div class="badge-pill">LA PLATAFORMA DE INVENTARIO DEL FUTURO</div>

            <h1 class="hero-title">
                Control de Inventario <br>
                <span class="text-gradient">Simplificado e Inteligente</span>
            </h1>

            <p class="hero-subtitle">
                Transforma la gestión de tu inventario con la plataforma más robusta para el control de productos, ventas y análisis de stock en tiempo real.
            </p>

            <div class="hero-buttons">
                <a href="registro.php" class="btn-primary" style="padding: 0.8rem 2rem;">
                    Comenzar Ahora
                </a>
                <a href="#caracteristicas" class="btn-outline" style="padding: 0.8rem 2rem;">
                    Conoce Más <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-left: 4px;"></i>
                </a>
            </div>
        </div>

        <!-- Mockup Right Side -->
        <div class="hero-mockup-container">
            <div class="mockup-blob"></div>
            <div class="hero-mockup">
                <img src="../IMG/dashboard-mockup.png" alt="Dashboard SIGI Mockup" style="object-position: top; height: 500px; object-fit: cover;">
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">+10k</div>
                <div class="stat-label">Negocios Activos</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">99%</div>
                <div class="stat-label">Satisfacción</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Soporte Técnico</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Datos Seguros</div>
            </div>
        </div>
    </section>

    <!-- Features Bento Box Section -->
    <section class="features-section" id="caracteristicas">
        <div class="features-header">
            <h2 class="features-title">Diseñado para el éxito comercial</h2>
            <p class="hero-subtitle" style="margin: 0 auto; font-size: 1rem;">
                Funcionalidades de nueva generación para optimizar el tiempo, controlar tus productos y multiplicar tus ventas.
            </p>
        </div>

        <div class="bento-grid">
            <div class="bento-card">
                <div>
                    <div class="feature-icon icon-blue"><i class="fas fa-bolt"></i></div>
                    <h3>Automatización Total y Reportes</h3>
                    <p style="max-width: 400px;">Reduce el tiempo de carga de productos y generación de reportes en un 60%. Enfócate en vender, no en la burocracia.</p>
                </div>
                <i class="fas fa-bolt bento-bg-img" style="font-size: 150px; color: #6366f1; opacity: 0.05;"></i>
            </div>

            <div class="bento-card">
                <div>
                    <div class="feature-icon icon-green"><i class="fas fa-shield-alt"></i></div>
                    <h3>Seguridad de Nivel Bancario</h3>
                    <p>Integridad y confidencialidad absoluta de tus registros de inventario.</p>
                </div>
                <i class="fas fa-lock bento-bg-img" style="font-size: 150px; color: #16a34a; opacity: 0.05;"></i>
            </div>

            <div class="bento-card">
                <div>
                    <div class="feature-icon icon-purple"><i class="fas fa-chart-line"></i></div>
                    <h3>Seguimiento</h3>
                    <p>Gráficos para detectar bajo stock en tiempo real.</p>
                </div>
                <i class="fas fa-chart-pie bento-bg-img" style="font-size: 150px; color: #9333ea; opacity: 0.05;"></i>
            </div>

            <div class="bento-card">
                <div>
                    <div class="feature-icon icon-orange"><i class="fas fa-users"></i></div>
                    <h3>Multiusuario</h3>
                    <p>Colabora con administradores y empleados fácilmente.</p>
                </div>
                <i class="fas fa-user-friends bento-bg-img" style="font-size: 150px; color: #ea580c; opacity: 0.05;"></i>
            </div>
        </div>
    </section>

    <!-- Nosotros Section -->
    <section class="about-section" id="nosotros">
        <div class="about-container">
            <div class="about-image">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Equipo de Trabajo">
                <div class="about-badge">
                    <div class="badge-icon"><i class="fas fa-check"></i></div>
                    <div class="badge-text" style="line-height: 1.2;">
                        <small style="font-size: 0.7rem; color: var(--text-gray); font-weight: 600;">Inventarios</small><br>
                        <strong style="color: var(--text-dark); font-size: 0.95rem;">Actualizados</strong>
                    </div>
                </div>
            </div>
            <div class="about-content">
                <div class="about-block">
                    <div class="about-icon" style="background: #eef2ff; color: #4F46E5;"><i class="fas fa-bullseye"></i></div>
                    <h3>Nuestra Misión</h3>
                    <p>Proveer una herramienta tecnológica intuitiva que facilite el proceso de gestión de inventario, promoviendo el control oportuno y el crecimiento continuo de tu negocio, conectando a administradores y operarios en un solo ecosistema seguro.</p>
                </div>
                <div class="about-block">
                    <div class="about-icon" style="background: #dcfce7; color: #10b981;"><i class="fas fa-eye"></i></div>
                    <h3>Nuestra Visión</h3>
                    <p>Ser el sistema de gestión comercial referente a nivel nacional, reconocido por su innovación constante, seguridad inquebrantable de datos y capacidad para transformar la información de ventas en historias reales de éxito empresarial.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-content">
            <h2>¿Listo para transformar tu negocio?</h2>
            <p>Únete a cientos de empresas que ya están optimizando su gestión comercial, controlando sus finanzas y aumentando sus ventas con SIGI.</p>
            <a href="/SIGI/views/Usuario/login.php" class="btn-white">Acceder a la plataforma <i class="fas fa-arrow-right"></i></a>
        </div>
    </section>

    <!-- Footer Completo -->
    <footer class="footer-dark">
        <div class="footer-container">
            <div class="footer-brand">
                <a href="index.php" class="nav-brand" style="color: white; margin-bottom: 1rem; display: flex;">
                    <div class="nav-brand-icon" style="background: var(--primary-blue);"><i class="fas fa-store"></i></div>
                    SIGI
                </a>
                <p class="footer-desc">Innovación tecnológica para los negocios del futuro. Creando puentes entre el control de stock y el éxito financiero a través de herramientas de gestión de primer nivel.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <h4 class="footer-title">ENLACES RÁPIDOS</h4>
                <ul>
                    <li><a href="#inicio">Inicio</a></li>
                    <li><a href="#caracteristicas">Características</a></li>
                    <li><a href="#nosotros">Nosotros</a></li>
                    <li><a href="#">Soporte Técnico</a></li>
                </ul>
            </div>

            <div class="footer-contact">
                <h4 class="footer-title">CONTACTO</h4>
                <ul>
                    <li><i class="fas fa-envelope text-primary"></i> contacto@sigi.com</li>
                    <li><i class="fas fa-phone text-primary"></i> +57 (300) 123-4567</li>
                    <li><i class="fas fa-map-marker-alt text-primary"></i> 123 Innovation Drive,<br>Tech District, 10001</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 SIGI Sistema de Gestión Comercial. Todos los derechos reservados.</p>
            <div class="footer-legal">
                <a href="#">Términos de Servicio</a>
                <a href="#">Política de Privacidad</a>
            </div>
        </div>
    </footer>

</body>

</html>