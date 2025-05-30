<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar si hay ID en la URL
if (!isset($_GET['id'])) {
    header("Location: inventario.php");
    exit();
}

$id = $_GET['id'];

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tienda_inventario");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener datos del producto
$sql = "SELECT * FROM productos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();

if (!$producto) {
    echo "Producto no encontrado.";
    exit();
}

// Si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];

    $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, cantidad = ? WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssdii", $nombre, $descripcion, $precio, $cantidad, $id);

    if ($stmt->execute()) {
        header("Location: inventario.php?mensaje=editado");
        exit();
    } else {
        echo "Error al actualizar el producto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Materialize CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
</head>
<body>

  <nav class="blue">
    <div class="nav-wrapper">
      <a href="#" class="brand-logo center">Editar Producto</a>
    </div>
  </nav>

  <div class="container" style="margin-top: 40px;">
    <h5>Modificar datos del producto</h5>
    <form method="POST">
      <div class="input-field">
        <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" required>
        <label class="active">Nombre</label>
      </div>

      <div class="input-field">
        <input type="text" name="descripcion" value="<?= htmlspecialchars($producto['descripcion']) ?>" required>
        <label class="active">Descripción</label>
      </div>

      <div class="input-field">
        <input type="number" name="precio" step="0.01" value="<?= $producto['precio'] ?>" required>
        <label class="active">Precio</label>
      </div>

      <div class="input-field">
        <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>" required>
        <label class="active">Cantidad</label>
      </div>

      <button type="submit" class="btn green">Guardar cambios</button>
      <a href="inventario.php" class="btn grey">Cancelar</a>
    </form>
  </div>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

</body>
</html>