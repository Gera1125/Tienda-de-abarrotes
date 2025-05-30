<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tienda_inventario");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta de ventas con JOIN para obtener nombre del producto
$sql = "SELECT v.id, p.nombre AS producto, v.cantidad, v.total, v.fecha_venta 
        FROM ventas v
        JOIN productos p ON v.producto_id = p.id
        ORDER BY v.fecha_venta DESC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Historial de Ventas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Materialize CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
  <!-- Iconos -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="blue">
  <div class="nav-wrapper">
    <a href="#" data-target="menu" class="sidenav-trigger left">
      <i class="material-icons">menu</i>
    </a>
    <a href="#" class="brand-logo center">Historial de Ventas</a>
  </div>
</nav>

<!-- Menú lateral -->
<ul class="sidenav" id="menu">
  <li><a href="../vendedor.html"><i class="material-icons">home</i>Inicio</a></li>
  <li><a href="inventario.php"><i class="material-icons">inventory</i>Inventario</a></li>
  <li><a href="registrar_venta.php"><i class="material-icons">add_shopping_cart</i>Registrar venta</a></li>
  <li><a href="historial_ventas.php"><i class="material-icons">history</i>Historial de ventas</a></li>
  <li><a href="logout.php"><i class="material-icons">exit_to_app</i>Cerrar sesión</a></li>
</ul>

<!-- Contenido principal -->
<div class="container" style="margin-top: 30px;">
  <h5>Listado de Ventas</h5>

  <!-- Botón de regreso al panel principal -->
  <a href="../vendedor.html" class="btn blue" style="margin-bottom: 20px;">
    <i class="material-icons left">arrow_back</i>Volver al inicio
  </a>

  <table class="striped responsive-table">
    <thead>
      <tr>
        <th>ID Venta</th>
        <th>Producto</th>
        <th>Cantidad</th>
        <th>Total</th>
        <th>Fecha</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($venta = $resultado->fetch_assoc()): ?>
        <tr>
          <td><?= $venta['id'] ?></td>
          <td><?= htmlspecialchars($venta['producto']) ?></td>
          <td><?= $venta['cantidad'] ?></td>
          <td>$<?= number_format($venta['total'], 2) ?></td>
          <td><?= $venta['fecha_venta'] ?></td>
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