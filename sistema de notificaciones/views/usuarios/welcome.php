<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Calificación en Línea</title>
    <link rel="shortcut icon" href="IMG/Icono.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); }
        .slide { display: none; }
        .slide.active { display: block; animation: fadeIn 0.8s; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <img src="IMG/Logo.png"class="w-28 h-14"  alt="Logo">
                </div>
                <div class="hidden md:flex space-x-8 font-medium">
                    <a href="#inicio" class="hover:text-blue-600 transition">Inicio</a>
                    <a href="#nosotros" class="hover:text-blue-600 transition">Nosotros</a>
                    <a href="#objetivos" class="hover:text-blue-600 transition">Objetivos</a>
                     <a href="login.php" 
                     class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Ingresar</a>
                </div>
            </div>
        </div>
    </nav>

    <section id="inicio" class="relative h-[500px] overflow-hidden text-white">
        <div class="slide active relative h-full">
            <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80" class="absolute inset-0 w-full h-full object-cover brightness-50" alt="Estudiantes">
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Gestión Académica Simplificada</h1>
                <p class="text-xl max-w-2xl">La plataforma más robusta para el seguimiento y evaluación del rendimiento escolar en tiempo real.</p>
            </div>
        </div>
        <div class="slide relative h-full">
            <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80" class="absolute inset-0 w-full h-full object-cover brightness-50" alt="Estudiantes">
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Transparencia y Precisión</h1>
                <p class="text-xl max-w-2xl">Resultados inmediatos para padres, docentes y estudiantes con solo un clic.</p>
            </div>
        </div>
        <button onclick="changeSlide(-1)" class="absolute left-4 top-1/2 z-20 bg-black/30 p-3 rounded-full hover:bg-black/50"><i class="fas fa-chevron-left"></i></button>
        <button onclick="changeSlide(1)" class="absolute right-4 top-1/2 z-20 bg-black/30 p-3 rounded-full hover:bg-black/50"><i class="fas fa-chevron-right"></i></button>
    </section>

    <section id="nosotros" class="py-20 max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12">
            <div class="bg-white p-8 rounded-2xl shadow-sm border-t-4 border-blue-600">
                <div class="text-blue-600 text-3xl mb-4"><i class="fas fa-bullseye"></i></div>
                <h2 class="text-2xl font-bold mb-4">Nuestra Misión</h2>
                <p class="text-gray-600 leading-relaxed">
                    Proveer una herramienta tecnológica intuitiva que facilite el proceso de calificación, promoviendo la retroalimentación oportuna y el crecimiento académico continuo de nuestra comunidad educativa.
                </p>
            </div>
            <div class="bg-white p-8 rounded-2xl shadow-sm border-t-4 border-blue-400">
                <div class="text-blue-400 text-3xl mb-4"><i class="fas fa-eye"></i></div>
                <h2 class="text-2xl font-bold mb-4">Nuestra Visión</h2>
                <p class="text-gray-600 leading-relaxed">
                    Ser el sistema de gestión académica referente a nivel nacional, reconocido por su innovación, seguridad de datos y capacidad para transformar la información en éxito escolar.
                </p>
            </div>
        </div>
    </section>

    <section id="objetivos" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold">Objetivos Estratégicos</h2>
                <div class="w-24 h-1 bg-blue-600 mx-auto mt-4"></div>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Automatización</h3>
                    <p class="text-gray-600">Reducir el tiempo de carga de notas en un 60% para los docentes.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Seguridad</h3>
                    <p class="text-gray-600">Garantizar la integridad y confidencialidad de los registros académicos.</p>
                </div>
                <div class="text-center p-6">
                    <div class="w-16 h-16 gradient-bg rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-4">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Seguimiento</h3>
                    <p class="text-gray-600">Facilitar análisis estadísticos para detectar estudiantes en riesgo.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-12">
            <div>
                <h4 class="text-white text-xl font-bold mb-4">EduGrade</h4>
                <p class="text-sm">Innovación tecnológica para la educación del futuro. Tu éxito comienza con una buena gestión.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Enlaces Rápidos</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-blue-400">Soporte Técnico</a></li>
                    <li><a href="#" class="hover:text-blue-400">Manual de Usuario</a></li>
                    <li><a href="#" class="hover:text-blue-400">Política de Privacidad</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-4">Contacto</h4>
                <p class="text-sm"><i class="fas fa-envelope mr-2"></i> soporte@edugrade.com</p>
                <p class="text-sm mt-2"><i class="fas fa-phone mr-2"></i> +57 3213076780</p>
                <div class="flex space-x-4 mt-4">
                    <a href="#" class="text-xl hover:text-white"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-xl hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-xl hover:text-white"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-800 mt-12 pt-8 text-center text-xs">
            &copy; 2026 EduGrade Sistema de Calificación. Todos los derechos reservados.
        </div>
    </footer>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');

        function changeSlide(direction) {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + direction + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        // Auto slide cada 5 segundos
        setInterval(() => changeSlide(1), 5000);
    </script>
</body>
</html>