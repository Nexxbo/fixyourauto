<?php
session_start();
require_once '../mvcmodelos/PiezaModel.php';
require_once '../mvcmodelos/UsuarioModel.php';

$db = new PDO('mysql:host=localhost:3306;dbname=fixyourauto', 'root', '');
$piezaModel = new PiezaModel($db);
$usuarioModel = new UsuarioModel($db);

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pieza = $piezaModel->getPiezaById($id);

    if(!$pieza) {
        header("Location: search.php");
        exit;
    }
} else {
    header("Location: search.php");
    exit;
}

$guardado_exito = false; // Variable para indicar éxito al guardar

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'guardar_pieza') {
    if(isset($_SESSION['usuario'])) {
        $usuario_id = $_SESSION['usuario']['id'];
        $pieza_id = $pieza['id']; // ID de la pieza de la página actual
        $cantidad_guardar = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

        if ($usuarioModel->guardarPiezaGuardada($usuario_id, $pieza_id, $cantidad_guardar)) {
            $guardado_exito = true;
        } else {
            $error_guardar = "Error al guardar la pieza. Inténtalo de nuevo.";
        }
    } else {
        echo "<script>alert('Inicia sesión primero para guardar piezas.');</script>";
    }
}


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Pieza - FixYourAuto</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/detalles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<header class="header-main">
    <div class="logo">
        <a href="../"><img id="logo-header" src="../img/logo.png" alt="Logo FixYourAuto"></a>
    </div>
    <nav>
        <ul>
            <li><a href="../">Inicio</a></li>
            <li><a href="./search.php">Buscar Piezas</a></li>
            <li><a href="https://club.autodoc.es/manuals">Guías de Reparación</a></li>
            <li>
                <?php if(isset($_SESSION['usuario'])): ?>
                    <a href="guardados.php">Mis Guardados</a>
                <?php else: ?>
                    <a href="#" onclick="alert('Inicia sesión primero para ver tus guardados.'); return false;">Mis Guardados</a>
                <?php endif; ?>
            </li>
            <?php if(isset($_SESSION['usuario'])): ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <span class="user-info">
                            <span><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="./clientoptions.php">Opciones de Cliente</a></li>
                        <li><a href="./logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="./login.php" class="btn-login">Acceso Clientes</a></li>
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

<main class="detalle-pieza">
    <div class="technical-header">
        <h1><?= htmlspecialchars($pieza['nombre']) ?></h1>
        <p class="codigo-oem">Referencia: <?= $pieza['numero_serie'] ?></p>
    </div>

    <div class="detalle-grid">
        <div class="gallery">
            <img src="../img/piezas/<?= $pieza['imagen'] ?>" class="main-image">
        </div>

        <div class="specs">
            <div class="specs-box">
                <h3>Especificaciones técnicas</h3>
                <ul class="specs-list">
                    <li><strong>Fabricante:</strong> <?= $pieza['marca'] ?></li>
                    <li><strong>Modelos compatibles:</strong> <?= $pieza['modelo_coche'] ?></li>
                    <li><strong>Peso:</strong> 2.4 kg</li>
                    <li><strong>Materiales:</strong> Aleación de aluminio</li>
                </ul>
            </div>

            <div class="order-box">
                <p class="precio-profesional">Precio profesional: <?= number_format($pieza['precio'], 2) ?>€</p>
                <p class="iva-nota">(Exento de IVA para talleres registrados)</p>
                <form method="post">
                    <input type="hidden" name="action" value="guardar_pieza">
                    <div class="cantidad-pedido">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" id="cantidad" name="cantidad" min="1" value="1">
                    </div>
                    <button type="submit" class="btn-pedido">Añadir a guardados</button>
                    <?php if ($guardado_exito): ?>
                        <p class="success-message">¡Pieza guardada en Mis Guardados!</p>
                    <?php elseif (isset($error_guardar)): ?>
                        <p class="error-message"><?php echo htmlspecialchars($error_guardar); ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
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