<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once '../mvcmodelos/UsuarioModel.php';
$db = new PDO('mysql:host=localhost:3306;dbname=fixyourauto', 'root', '');
$usuarioModel = new UsuarioModel($db);

$usuario_session = $_SESSION['usuario'];
$usuario_db = $usuarioModel->getUsuarioById($usuario_session['id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'update_nombre') {
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            if (empty($nombre)) {
                $error = 'Error: El nombre no puede estar vacío.';
            } else {
                if ($usuarioModel->updateNombreUsuario($usuario_session['id'], $nombre)) {
                    $_SESSION['usuario']['nombre'] = $nombre; // Actualizar nombre en sesión
                    $success = 'Nombre actualizado correctamente.';
                } else {
                    $error = 'Error al actualizar el nombre. Inténtalo de nuevo.';
                }
            }
        } elseif ($action == 'update_password') {
            $password_actual = isset($_POST['password_actual']) ? trim($_POST['password_actual']) : '';
            $password_nuevo = isset($_POST['password_nuevo']) ? trim($_POST['password_nuevo']) : '';
            $password_confirmar = isset($_POST['password_confirmar']) ? trim($_POST['password_confirmar']) : '';

            if (empty($password_actual) || empty($password_nuevo) || empty($password_confirmar)) {
                $error = 'Error: Todos los campos de contraseña son obligatorios.';
            } elseif ($password_nuevo !== $password_confirmar) {
                $error = 'Error: Las nuevas contraseñas no coinciden.';
            } elseif ($usuario_db['password'] !== $password_actual) { // Compare with password from DB
                $error = 'Error: Contraseña actual incorrecta.';
            } else {
                if ($usuarioModel->updatePasswordUsuario($usuario_session['id'], $password_nuevo)) {
                    $_SESSION['usuario']['password'] = $password_nuevo; // Update password in session
                    $success = 'Contraseña actualizada correctamente.';
                } else {
                    $error = 'Error al actualizar la contraseña. Inténtalo de nuevo.';
                }
            }
        } elseif ($action == 'delete_account') {
            if ($usuarioModel->deleteUsuario($usuario_session['id'])) {
                session_unset();
                session_destroy();
                header("Location: ../main.php");
                exit;
            } else {
                $error = 'Error al eliminar la cuenta. Inténtalo de nuevo.';
            }
        }
    }
}
$usuario = $usuario_session;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Opciones de Cliente - FixYourAuto</title>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/clientoptions.css">
</head>
<body>

<header class="header-main">
    <div class="logo">
        <a href="../"><img src="../img/logo.png" alt="FixYourAuto Logo"></a>
    </div>
    <nav>
        <ul>
            <li><a href="../">Inicio</a></li>
            <li><a href="search.php">Buscar Piezas</a></li>
            <li>
                <?php if(isset($_SESSION['usuario'])): ?>
                    <a href="guardados.php">Mis Guardados</a>
                <?php else: ?>
                    <a href="#" onclick="alert('Inicia sesión primero para ver tus guardados.'); return false;">Mis Guardados</a>
                <?php endif; ?>
            </li>            <?php if (isset($_SESSION['usuario'])): ?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">
                        <span class="user-info">
                            <span><?php echo htmlspecialchars($_SESSION['usuario']['nombre']); ?></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="clientoptions.php">Opciones de Cliente</a></li>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Acceso Profesionales</a></li>
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

<main class="client-options-main">
    <section class="user-info-section">
        <h2>Opciones de Cliente</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="user-details">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?></p>
        </div>

        <section class="option-section">
            <h3>Modificar Nombre</h3>
            <form method="post">
                <input type="hidden" name="action" value="update_nombre">
                <div class="form-group">
                    <label for="nombre">Nombre Actual:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                </div>
                <button type="submit" class="btn-update">Actualizar Nombre</button>
            </form>
        </section>

        <section class="option-section">
            <h3>Modificar Contraseña</h3>
            <form method="post">
                <input type="hidden" name="action" value="update_password">
                <div class="form-group">
                    <label for="password_actual">Contraseña Actual:</label>
                    <input type="password" id="password_actual" name="password_actual" required>
                </div>
                <div class="form-group">
                    <label for="password_nuevo">Nueva Contraseña:</label>
                    <input type="password" id="password_nuevo" name="password_nuevo" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmar">Confirmar Nueva Contraseña:</label>
                    <input type="password" id="password_confirmar" name="password_confirmar" required>
                </div>
                <button type="submit" class="btn-update">Cambiar Contraseña</button>
            </form>
        </section>

        <section class="option-section delete-account-section">
            <h3>Eliminar Cuenta</h3>
            <p class="warning-text">¡Cuidado! Esta acción es irreversible. Una vez eliminada tu cuenta, perderás todos tus datos y acceso.</p>
            <form method="post" onsubmit="return confirm('¿Estás seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.');">
                <input type="hidden" name="action" value="delete_account">
                <button type="submit" class="btn-delete-account">Eliminar Cuenta</button>
            </form>
        </section>

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