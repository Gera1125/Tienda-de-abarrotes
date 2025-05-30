<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tienda_inventario");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Procesar formulario de agregar producto
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $fecha = date('Y-m-d');

    $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, cantidad, fecha_agregado) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $cantidad, $fecha);
    $stmt->execute();
    header("Location: inventario.php?mensaje=agregado");
    exit();
}

// Consulta de productos
$sql = "SELECT * FROM productos";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Materialize CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
  <!-- Iconos -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    .sidenav-trigger {
      display: inline-block !important;
    }

    @media (min-width: 993px) {
      .sidenav-trigger {
        display: inline-block !important;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="blue">
  <div class="nav-wrapper">
    <a href="#" data-target="menu" class="sidenav-trigger left">
      <i class="material-icons">menu</i>
    </a>
    <a href="#" class="brand-logo center">Inventario</a>
  </div>
</nav>

<!-- Menú lateral -->
<ul class="sidenav" id="menu">
  <li><a href="../vendedor.html"><i class="material-icons">home</i>Inicio</a></li>
  <li><a href="inventario.php"><i class="material-icons">inventory</i>Inventario</a></li>
  <li><a href="registrar_venta.php"><i class="material-icons">shopping_cart</i>Registrar venta</a></li>
  <li><a href="logout.php"><i class="material-icons">exit_to_app</i>Cerrar sesión</a></li>
</ul>

<!-- Contenido principal -->
<div class="container" style="margin-top: 30px;">
  <h4>Inventario de Productos</h4>

  <!-- Mensajes de estado -->
  <?php if (isset($_GET['mensaje'])): ?>
    <div class="card-panel green lighten-4 green-text text-darken-4">
      <?php
        if ($_GET['mensaje'] === 'agregado') echo "✅ Producto agregado correctamente.";
        if ($_GET['mensaje'] === 'eliminado') echo "🗑️ Producto eliminado correctamente.";
        if ($_GET['mensaje'] === 'editado') echo "✏️ Producto actualizado correctamente.";
      ?>
    </div>
  <?php endif; ?>

  <!-- Formulario para agregar producto -->
  <h5>Agregar nuevo producto</h5>
  <form method="POST" action="inventario.php">
    <div class="row">
      <div class="input-field col s12 m3">
        <input type="text" name="nombre" id="nombre" required>
        <label for="nombre">Nombre</label>
      </div>
      <div class="input-field col s12 m3">
        <input type="text" name="descripcion" id="descripcion" required>
        <label for="descripcion">Descripción</label>
      </div>
      <div class="input-field col s12 m2">
        <input type="number" step="0.01" name="precio" id="precio" required>
        <label for="precio">Precio</label>
      </div>
      <div class="input-field col s12 m2">
        <input type="number" name="cantidad" id="cantidad" required>
        <label for="cantidad">Cantidad</label>
      </div>
      <div class="input-field col s12 m2">
        <button type="submit" name="agregar" class="btn green">
          <i class="material-icons left">add</i>Agregar
        </button>
      </div>
    </div>
  </form>

  <!-- Tabla de productos -->
  <h5 class="mt-4">Lista de Productos</h5>
  <table class="striped responsive-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Precio</th>
        <th>Cantidad</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($producto = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= $producto['id'] ?></td>
          <td><?= htmlspecialchars($producto['nombre']) ?></td>
          <td><?= htmlspecialchars($producto['descripcion']) ?></td>
          <td>$<?= number_format($producto['precio'], 2) ?></td>
          <td><?= $producto['cantidad'] ?></td>
          <td><?= $producto['fecha_agregado'] ?></td>
          <td>
            <a href="editar_producto.php?id=<?= $producto['id'] ?>" class="btn-small orange">
              <i class="material-icons">edit</i>
            </a>
            <a href="eliminar_producto.php?id=<?= $producto['id'] ?>" class="btn-small red" onclick="return confirm('¿Eliminar este producto?');">
              <i class="material-icons">delete</i>
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    M.Sidenav.init(document.querySelectorAll('.sidenav'));
  });
</script>

</body>
</html>