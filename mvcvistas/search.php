<?php
session_start();
require_once '../mvcmodelos/PiezaModel.php';
$db = new PDO( 'mysql:host=localhost:3306;dbname=fixyourauto', 'root', '' );
$piezaModel = new PiezaModel($db);

// Obtener marcas únicas para el filtro
$marcas = $piezaModel->getMarcas();


$filtros = array(
    'query' => !empty($_GET['query']) ? $_GET['query'] : '',
    'marca' => !empty($_GET['marca']) ? $_GET['marca'] : '',
    'modelo_coche' => !empty($_GET['modelo_coche']) ? $_GET['modelo_coche'] : '',
    'precio_min' => !empty($_GET['precio_min']) ? $_GET['precio_min'] : '',
    'precio_max' => !empty($_GET['precio_max']) ? $_GET['precio_max'] : '',
    'stock' => !empty($_GET['stock']) ? $_GET['stock'] : '',
    'tipo_pieza' => !empty($_GET['tipo_pieza']) ? $_GET['tipo_pieza'] : '',
    'nombre-pieza' => !empty($_GET['nombre-pieza']) ? $_GET['nombre-pieza'] : '',
    'sort' => !empty($_GET['sort']) ? $_GET['sort'] : 'precio_asc'

);

$piezas = $piezaModel->getPiezas($filtros);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda - FixYourAuto</title>
    <link rel="stylesheet" href="../css/search.css">
    <script src="../js/jquery-1.11.1.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>

<body>
<header class="search-header">
    <div class="logo">
        <a href="../"><img id="logo-header" src="../img/logo.png" alt="Logo FixYourAuto"></a>
    </div>
    <nav>
        <ul>
            <li><a href="../">Inicio</a></li>
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
                        <li><a href="clientoptions.php">Opciones de Cliente</a></li>
                        <li><a href="logout.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="login.php" class="btn-login">Acceso Clientes</a></li>
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

<main class="search-container">
    <aside class="filters-sidebar">
        <div class="filter-section">
            <h3 class="filter-title active">Filtros de Búsqueda<span class="toggle-icon">▼</span></h3>
            <form id="filter-form" class="filter-content show" method="GET">

                <div class="filter-group">
                    <h4>Marca de la pieza</h4>
                    <select name="marca" class="filter-select">
                        <option value="">Todas</option>
                        <?php foreach($marcas as $marca): ?>
                            <option value="<?= htmlspecialchars($marca) ?>"><?= $marca ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <h4>Nombre de la pieza</h4>
                    <input type="text"
                           name="nombre-pieza"
                           placeholder="Ej: Pinzas de freno"
                           class="filter-input"
                           value="<?= htmlspecialchars($filtros['nombre-pieza']) ?>">
                </div>

                <div class="filter-group">
                    <h4>Modelo del Coche</h4>
                    <input type="text" name="modelo_coche" placeholder="Ej: Audi A4 2015"
                           class="filter-input" value="<?= htmlspecialchars($filtros['modelo_coche']) ?>">
                </div>



                <div class="filter-group">
                    <h4>Rango de Precio (€)</h4>
                    <div class="price-range">
                        <input type="number" name="precio_min" placeholder="Mínimo"
                               value="<?= htmlspecialchars($filtros['precio_min']) ?>">
                        <span>-</span>
                        <input type="number" name="precio_max" placeholder="Máximo"
                               value="<?= htmlspecialchars($filtros['precio_max']) ?>">
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Disponibilidad</h4>
                    <label class="filter-checkbox">
                        <input type="checkbox" name="stock" value="1" <?= $filtros['stock'] ? 'checked' : '' ?>>
                        En stock inmediato
                    </label>
                </div>

                <button type="submit" class="btn-filtrar">Aplicar Filtros</button>
            </form>
        </div>
    </aside>

    <section class="results-section">
        <div class="results-header">
            <h2><?= count($piezas) ?> Piezas encontradas</h2>
            <div class="sort-filter">
                <select class="sort-select" name="sort">
                    <option value="precio_asc" <?= ($filtros['sort'] == 'precio_asc') ? 'selected' : '' ?>>Precio más bajo</option>
                    <option value="precio_desc" <?= ($filtros['sort'] == 'precio_desc') ? 'selected' : '' ?>>Precio más alto</option>
                    <option value="recientes" <?= ($filtros['sort'] == 'recientes') ? 'selected' : '' ?>>Más recientes</option>
                </select>
            </div>
        </div>

        <div class="results-grid">
            <?php foreach ($piezas as $pieza): ?>
                <article class="pieza-card">
                    <div class="pieza-badge"><?= $pieza['stock'] > 0 ? '🚚 En stock' : '⏳ Bajo pedido' ?></div>
                    <div class="pieza-image">
                        <?php if(!empty($pieza['imagen'])): ?>
                            <img src="../img/piezas/<?= $pieza['imagen'] ?>" alt="<?= $pieza['nombre'] ?>">
                        <?php else: ?>
                            <div class="image-placeholder">
                                <span>Imagen no disponible</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="pieza-info">
                        <h3><?= htmlspecialchars($pieza['nombre']) ?></h3>
                        <div class="pieza-meta">
                            <span class="codigo-oem">REF: <?= $pieza['numero_serie'] ?></span>
                            <span class="marca"><?= $pieza['marca'] ?></span>
                        </div>
                        <div class="compatibility">
                            <strong>Compatibilidad:</strong> <?= $pieza['modelo_coche'] ?>
                        </div>
                        <div class="pieza-pricing">
                            <p class="precio"><?= number_format($pieza['precio'], 2) ?> € <span class="iva">+ IVA</span></p>
                            <div class="profesional-price">
                                <span>Precio profesional: <?= number_format($pieza['precio'] * 0.85, 2) ?> €</span>
                            </div>
                        </div>
                        <a href="detalles.php?id=<?= $pieza['id'] ?>" class="btn-detalle">Ver detalles</a>
                    </div>
                </article>
            <?php endforeach; ?>
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

<script>
    $(document).ready(function(){
        $('.filter-title').click(function(){
            $(this).toggleClass('active').next('.filter-content').toggleClass('show');
            $(this).find('.toggle-icon').text($(this).hasClass('active') ? '▼' : '▶');
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('.sort-select').change(function(){
            $('#filter-form').append('<input type="hidden" name="sort" value="'+$(this).val()+'">');
            $('#filter-form').submit();
        });
    });
</script>
</body>
</html>