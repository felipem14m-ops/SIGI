<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="CSS/Style.css">
    <link rel="shortcut icon" href="IMG/Logo.jpg">
    <title>Programacion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .col-md-4 img {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 10px;
        text-align: center;
      }
      .separador {
        border: 2px solid #dee2e6;
        margin: 30px 0;
      }
       body {
      background-color: #f0f4f8;
      }
      #carouselExampleAutoplaying{
       max-height: 300px;
       overflow: hidden;
      }

       #carouselExampleAutoplaying img{
        height: 300px;
        object-fit: cover;
      }  
      
    </style>
</head>
<body>

<!-- CARRUSEL -->
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="IMG/IMG1.jpeg" class="d-block w-100" alt="Imagen 1">
    </div>
    <div class="carousel-item">
      <img src="IMG/IMG2.jpeg" class="d-block w-100" alt="Imagen 2">
    </div>
    <div class="carousel-item">
      <img src="IMG/IMG3.jpeg" class="d-block w-100" alt="Imagen 3">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>
<!-- FIN CARRUSEL -->

<!--  CONTENIDO -->
<div class="container mt-5">

  <!--FILA 1-->
  <div class="row text-center g-4">

    <!-- Misión -->
    <div class="col-md-4">
      <h2>La programación 📱</h2>
      <img src="IMG/IMG1.png" alt="Programacion">
      <p>La programación es el proceso de diseñar, codificar y mantener el conjunto 
        de instrucciones que le indican a una computadora cómo realizar tareas específicas.</p>
    </div>

    <!-- Que es PHP -->
    <div class="col-md-4">
      <h2>PHP 👾</h2>
      <img src="IMG/PHP.jpg" alt="PHP">
      <p>PHP es un lenguaje de programación del lado del servidor utilizado 
        principalmente para el desarrollo web y la conexión con bases de datos.</p>
    </div>

    <!-- Objeto -->
    <div class="col-md-4">
      <h2>Guitarra 🎸</h2>
      <img src="IMG/Guitarra.png" alt="Guitarra">
      <p>La guitarra es un instrumento de cuerda pulsada con caja de resonancia (acústica/española) o cuerpo sólido 
        (eléctrica), compuesto por mástil, diapasón con trastes y seis cuerdas.</p>
    </div>

  </div>
  <!-- FIN FILA 1 -->

  <hr class="separador">

  <!-- FILA 2-->
  <div class="row text-center g-4">

    <!-- Sistema de notas -->
    <div class="col-md-4 d-flex flex-column justify-content-center">
      <h2>Sistema de notas 🟰</h2>
      <p>En el siguiente formulario vas a ingresar tres notas y te dará el resultado.</p>
      <h5 class="text-danger">Para aprobar necesitas un promedio mayor a 3.0</h5>
    </div>

    <!-- Calculadora de notas -->
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          Calculadora de Notas
        </div>
        <div class="card-body">
          <form action="" method="post">
            <label class="form-label">Nota 1:</label>
            <input type="text" name="nota1" class="form-control mb-2" required>

            <label class="form-label">Nota 2:</label>
            <input type="text" name="nota2" class="form-control mb-2" required>

            <label class="form-label">Nota 3:</label>
            <input type="text" name="nota3" class="form-control mb-3" required>

            <button type="submit" class="btn btn-primary w-100">Calcular</button>
          </form>

          <?php
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
              $nota1 = floatval($_POST["nota1"]);
              $nota2 = floatval($_POST["nota2"]);
              $nota3 = floatval($_POST["nota3"]);
              $promedio = round(($nota1 + $nota2 + $nota3) / 3, 2);

              if ($promedio >= 3.0) {
                echo "<p class='mt-3 text-center'>Tu promedio es  $promedio <span style='color:green'> APROBADO</span></p>";
              } else {
                 echo "<p class='mt-3 text-center'>Tu promedio es $promedio <span style='color:red'> REPROBADO</span></p>";
              }
          }
          ?>
        </div>
      </div>
    </div>

    <!-- Video explicación -->
    <div class="col-md-4">
      <h2>Explicación PHP 👾</h2>
      <div class="ratio ratio-16x9">
        <iframe src="https://www.youtube.com/embed/ykGRYEX0n60?si=loDi9daI8ZKuTwGU" 
          title="YouTube video player" frameborder="0" 
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
          allowfullscreen></iframe>
      </div>
    </div>

  </div>
  <!-- FIN FILA 2 -->

</div>
<!-- FIN CONTENIDO -->

<!-- ================== FOOTER ================== -->
<footer class="text-center text-lg-start bg-body-tertiary text-muted mt-5">
  <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
    <div>
      <a href="" class="me-4 text-reset"><i class="fab fa-facebook-f"></i></a>
      <a href="" class="me-4 text-reset"><i class="fab fa-twitter"></i></a>
      <a href="" class="me-4 text-reset"><i class="fab fa-google"></i></a>
      <a href="" class="me-4 text-reset"><i class="fab fa-instagram"></i></a>
      <a href="" class="me-4 text-reset"><i class="fab fa-linkedin"></i></a>
      <a href="" class="me-4 text-reset"><i class="fab fa-github"></i></a>
    </div>
  </section>

  <section>
    <div class="container text-center text-md-start mt-5">
      <div class="row mt-3">

        <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">El ingeniero de la IA 🦾</h6>
          <p>Esto está hecho a pura cabeza 0% IA 🦾</p>
        </div>

        <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Products</h6>
          <p><a href="#!" class="text-reset">Angular</a></p>
          <p><a href="#!" class="text-reset">React</a></p>
          <p><a href="#!" class="text-reset">Vue</a></p>
          <p><a href="#!" class="text-reset">Laravel</a></p>
        </div>

        <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Useful links</h6>
          <p><a href="#!" class="text-reset">Pricing</a></p>
          <p><a href="#!" class="text-reset">Settings</a></p>
          <p><a href="#!" class="text-reset">Orders</a></p>
          <p><a href="#!" class="text-reset">Help</a></p>
        </div>

        <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-4">
          <h6 class="text-uppercase fw-bold mb-4">Contáctanos</h6>
          <p>📍 La Plata Huila, Colombia</p>
          <p>✉️ felipem14m@gmail.com</p>
          <p>📞 +57 3213076800</p>
          <p>🖨️ +57 3143270788</p>
        </div>

      </div>
    </div>
  </section>

  <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
    © 2026 <a class="text-reset fw-bold" href="#">El original.com</a>
  </div>
</footer>

</body>
</html>
