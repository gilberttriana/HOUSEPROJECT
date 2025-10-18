<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>

  @vite(['resources/js/app.js'])

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <title>HouseBuilde</title>
</head>
<body>
    <header>
        <div class="logo">
            <img src="{{ asset('image/HoseBuilde.png') }}" alt="logo">
        </div>

        <nav id="nav">
            <a href="#inicio" class="nav-link"><i class="fa fa-home"></i>Inicio</a>
            <a href="#services" class="nav-link"><i class="fa fa-cog"></i>Servicios</a>
            <a href="#contacto" class="nav-link"><i class="fa fa-phone" aria-hidden="true"></i>Contactanos</a>
            <a href="#" class=" login-btn" >Iniciar sesión</a>

        </nav>
    </header>

    <section id="inicio" class="hero">
        <div class="container">
            <h1>Bienvenido a HouseBuilde</h1>
            <p>Explora nuestra red de proveedores y accede fácilmente a productos
                 y servicios para tus proyectos de construcción con solo un click.</p>
            <a href="#" class="btn  login-btn" id="openLogin">Iniciar sesión</a>

             <div class="more-info">
            <a href="#mas" class="text-link">Más sobre el sitio</a>

            <a href="#mas" class="arrow-link">
                <i class="fa fa-arrow-down" aria-hidden="true"></i>
            </a>
        </div>
        </div>
    </section>

    <section id="mas" class="about-us">
    <div class="about-content">
        <div class="about-text">
            <h3>¿Quiénes Somos? <i class="fa-solid fa-lightbulb"></i></h3>
            <p>
                <span style="color: #e18011;">HouseBuild</span> es una plataforma innovadora
                pensada para aquellas personas que desean planificar o iniciar un proyecto de
                construcción sin necesidad de tener conocimientos previos sobre costos de materiales
                o disponibilidad en el mercado. Nuestro sistema integra catálogos actualizados de
                ferreterías e industrias locales, permitiendo a los usuarios comparar precios,
                calidad y variedad de productos en tiempo real. Además, <span style="color: #e18011;">HouseBuild</span>
                conecta a los clientes con profesionales activos del sector de la construcción,
                ofreciendo la posibilidad de establecer contratos confiables y transparentes con
                maestros de obra, arquitectos e ingenieros. De esta manera, buscamos simplificar
                la experiencia de construir, garantizando acceso a información clara,
                proveedores cercanos y especialistas calificados, todo en un solo lugar y al alcance de un clic.
            </p>
             <p>
                Nuestra meta es crear una comunidad digital donde cada proyecto,
                grande o pequeño, cuente con el respaldo de información actualizada,
                asesoría experta y las mejores opciones del mercado.
            </p>
        </div>

        <!-- Carrusel de imágenes -->
        <div class="about-carousel swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="image/house.jpg" alt="Imagen 1">
                </div>
                <div class="swiper-slide">
                    <img src="image/coronado.jpg" alt="Imagen 2">
                </div>
                <div class="swiper-slide">
                    <img src="" alt="Imagen 3">
                </div>
            </div>
            <!-- Botones de navegación -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <!-- Paginación -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    </section>


    <section id="services" class="services">
        <div class="container">
            <h2 class="section-title"><i class="fa fa-cogs"></i>Servicios</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3><i class="fa fa-user"></i>Perfil Usuario</h3>
                    <p>Crea tu cuenta, gestiona tus datos y accede fácilmente a nuestros catálogos y servicios.</p>
                </div>
                <div class="service-card">
                    <h3><i class="fa fa-desktop"></i>Proveedores</h3>
                    <p>Red de proveedores confiables de materiales de construcción, garantizando calidad y precios competitivos.</p>
                </div>
                <div class="service-card">
                    <h3><i class="fa fa-users"></i>Maestro de Obras</h3>
                    <p>Comunidad de profesionales en la construcción a tu disposición, compartiendo experiencia, confianza y calidad.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="projects">
        <div class="container">
            <h2 class="section-title"><i class="fa fa-archive"></i>Empresas Asociadas</h2>
            <div class="projects-grid">
                <div class="project-card">
                    <img src="{{ asset('image/coronado1.png') }}" alt="Commercial Project">
                </div>
                <div class="project-card">
                    <img src="{{ asset('image/leivaFer.png') }}" alt="Residential Project">
                </div>
                <div class="project-card">
                    <img src="{{ asset('image/richardson.jpeg') }}" alt="Renovation Project">
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div id="contacto" class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <div class="contact-info">
                        <p>800 deany Deletes</p>
                        <p>Phone: 400 96800</p>
                        <p>Email: info@housebuild.com</p>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#"><i class="fab fa-instagram"></i>Instagram</a></li>
                        <li><a href="#"><i class="fab fa-facebook"></i>Facebook</a></li>
                        <li><a href="#"><i class="fab fa-whatsapp"></i>WhatsApp</a></li>
                        <li><a href="#"><i class="fab fa-google"></i>Google</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <p>Follow us on social media for updates and inspiration.</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 HOUSEBUILD.COM - SU AGENTE DE CONFIANZA </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>


     <!-- Modal De Login -->
    <div class="alert-container" id="alertContainer"></div>

    <div id="loginModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="alert-container-modal" id="loginAlertContainer"></div>
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2>Iniciar Sesión</h2>
                <div class="logo1">
                    <img src="image/HoseBuilde.png" alt="MedTalk Logo">
                </div>
                <form id="loginForm" method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <input type="email" name="correo" placeholder="Correo" required autocomplete="off">
                    <div class="password-wrapper">
                            <input type="password" name="contrasena"id="contraseña" placeholder="Contraseña" required>
                    <span class="toggle-password" id="MOOD" onclick="togglePasswordVisibility()">
                        <i class="fas fa-eye-slash"></i>
                    </span>
                    </div>

                    <button type="submit" >Iniciar Sesión</button>
                    <p style="color: #c46a0e; font-size: 15px;">
                     No te has registrado?<a href="#" class="registri-btn">Click Aqui</a>
                    <a href="#" class="google-btn">
                    <i class="fab fa-google"></i>Continuar con Google
                        </a>
                    </p>

                </form>
                <p id="loginMessage"></p>
            </div>

        </div>

         <!-- Modal De Registro -->

        <div id="loginModal1" class="modal1" style="display: none;">
            <div class="modal-content1">
                  <div class="alert-container-modal" id="registerAlertContainer"></div>
                <span class="close-btn" onclick="closeModal1()">&times;</span>
                <h2>Registrate</h2>
                <div class="logo1">
                    <img src="image/HoseBuilde.png" alt="MedTalk Logo">
                </div>
                <form id="loginForm1" method="POST" action="{{ route('register.post') }}" >
                    @csrf
                    <div class="form-grid">
                        <input type="text" name="nombre" placeholder="Nombre" required autocomplete="off">
                        <input type="text" name="apellido" placeholder="Apellido" required autocomplete="off">
                        <input type="date" name="fecha_nac" placeholder="Nacimiento" required>

                        <input type="email" name="correo" placeholder="Correo" required autocomplete="off">
                        <input type="password" name="contrasena" placeholder="Contraseña" required>

                    <button type="submit">Registrar</button>
                </form>
                <p id="loginMessage1"></p>
            </div>
        </div>
   </div>

</body>
</html>
