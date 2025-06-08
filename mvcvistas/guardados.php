<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Inicia sesión primero para ver tus guardados.'); window.location.href='login.php';</script>";
    exit;
}

require_once '../mvcmodelos/UsuarioModel.php';
require_once '../mvcmodelos/PiezaModel.php';

$db = new PDO('mysql:host=localhost:3306;dbname=fixyourauto', 'root', '');
$usuarioModel = new UsuarioModel($db);
$piezaModel = new PiezaModel($db);

$usuario_id = $_SESSION['usuario']['id'];
$piezas_guardadas = $usuarioModel->getPiezasGuardadasPorUsuario($usuario_id);
$mensaje_eliminado = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'eliminar_guardado') {
    $guardado_id_eliminar = isset($_POST['guardado_id']) ? intval($_POST['guardado_id']) : 0;
    if ($guardado_id_eliminar > 0) {
        if ($usuarioModel->deletePiezaGuardada($guardado_id_eliminar)) {
            $mensaje_eliminado = "Pieza eliminada de Mis Guardados.";
            $piezas_guardadas = $usuarioModel->getPiezasGuardadasPorUsuario($usuario_id);
        } else {
            $mensaje_eliminado = "Error al eliminar la pieza. Inténtalo de nuevo.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Guardados - FixYourAuto</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/guardados.css">
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
            <li><a href="guardados.php">Mis Guardados</a></li>
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

<main class="guardados-container">
    <section class="guardados-listado">
        <h2>Mis Piezas Guardadas</h2>

        <?php if (!empty($mensaje_eliminado)): ?>
            <div class="mensaje-alerta"><?= htmlspecialchars($mensaje_eliminado) ?></div>
        <?php endif; ?>

        <?php if (empty($piezas_guardadas)): ?>
            <p class="mensaje-vacio">No has guardado ninguna pieza aún.</p>
        <?php else: ?>
            <div class="grid-guardados">
                <?php foreach ($piezas_guardadas as $guardado): ?>
                    <article class="pieza-guardada-card">
                        <div class="pieza-image">
                            <?php if(!empty($guardado['imagen'])): ?>
                                <img src="../img/piezas/<?= $guardado['imagen'] ?>" alt="<?= htmlspecialchars($guardado['nombre']) ?>">
                            <?php else: ?>
                                <div class="image-placeholder">
                                    <span>Imagen no disponible</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="pieza-info">
                            <h3><?= htmlspecialchars($guardado['nombre']) ?></h3>
                            <p class="marca"><?= htmlspecialchars($guardado['marca']) ?></p>
                            <p class="cantidad">Cantidad guardada: <?= htmlspecialchars($guardado['cantidad']) ?></p>
                            <p class="precio"><?= number_format($guardado['precio'], 2) ?> €</p>
                            <a href="detalles.php?id=<?= $guardado['pieza_id'] ?>" class="btn-detalle">Ver detalles</a>
                            <form method="post" class="form-eliminar-guardado" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta pieza de tus guardados?');">
                                <input type="hidden" name="action" value="eliminar_guardado">
                                <input type="hidden" name="guardado_id" value="<?= htmlspecialchars($guardado['id']) ?>">
                                <button type="submit" class="btn-eliminar-guardado">Eliminar</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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