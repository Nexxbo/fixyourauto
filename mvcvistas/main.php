<?php
session_start();
require_once 'mvcmodelos/PiezaModel.php';
$db = new PDO('mysql:host=localhost:3306;dbname=fixyourauto', 'root', '');
$piezaModel = new PiezaModel($db);
$marcas = $piezaModel->getMarcas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixYourAuto - Repuestos Profesionales</title>
    <link rel="stylesheet" href="css/main.css">
    <script src="js/jquery-1.11.1.min.js"></script>
</head>
<body>
<header class="header-main">
    <div class="logo">
        <a href="index.php"><img id="logo-header" src="img/logo.png" alt="Logo FixYourAuto"></a>
    </div>
    <nav>
        <ul>
            <li><a href="mvcvistas/search.php">Buscar Piezas</a></li>
            <li><a href="https://club.autodoc.es/manuals">Guías de Reparación</a></li>
            <li>
                <?php if(isset($_SESSION['usuario'])): ?>
                    <a href="mvcvistas/guardados.php">Mis Guardados</a>
                <?php else: ?>
                    <a href="#" onclick="alert('Inicia sesión primero para ver tus guardados.'); return false;">Mis Guardados</a>
                <?php endif; ?>
            </li>
            <?php if (isset($_SESSION['usuario'])): ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <span class="user-info">
                            <span><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="mvcvistas/clientoptions.php">Opciones de Cliente</a></li>
                        <li><a href="mvcvistas/logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="mvcvistas/login.php" class="btn-login">Acceso Clientes</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.querySelector('.dropdown-toggle');
            const dropdownMenu = document.querySelector('.dropdown-menu');

            if (dropdownToggle && dropdownMenu) {
                dropdownToggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    dropdownMenu.classList.toggle('show');
                });

                document.addEventListener('click', function(event) {
                    if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</header>

<main>
    <section class="hero-banner">
        <h1>Encuentra piezas técnicas profesionales</h1>
        <form id="search-form" action="mvcvistas/search.php" method="GET">
            <input type="text" name="modelo_coche" placeholder="Ej: Audi A4 2015" required>
            <div class="filters">
                <select name="marca">
                    <option value="">Marca de la pieza</option>
                    <?php foreach($marcas as $marca): ?>
                        <option value="<?= htmlspecialchars($marca) ?>"><?= $marca ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="nombre-pieza" placeholder="Nombre de la pieza">
                <button type="submit">Buscar ahora</button>
            </div>
        </form>
    </section>


    <section class="categorias-destacadas">
        <h2>Partes técnicas destacadas</h2>
        <div class="grid-categorias">
            <div class="categoria-item">
                <img src="img/motor-icon.png" alt="Motor">
                <h3>Componentes de Motor</h3>
            </div>
            <div class="categoria-item">
                <img src="img/frenos-icon.png" alt="Frenos">
                <h3>Sistemas de Frenado</h3>
            </div>
            <div class="categoria-item">
                <img src="img/transmision-icon.png" alt="Transmisión">
                <h3>Transmisiones</h3>
            </div>
        </div>
    </section>
</main>

<footer class="footer-main">
    <div class="footer-section">
        <h4>Para profesionales</h4>
        <ul>
            <li><a href="#">Catálogo técnico</a></li>
            <li><a href="#">Pedidos al mayor</a></li>
            <li><a href="#">Garantías profesionales</a></li>
        </ul>
    </div>
    <div class="footer-copyright">
        <p>&copy; 2025 FixYourAuto - Proveedor certificado de piezas técnicas</p>
    </div>
</footer>
</body>
</html>