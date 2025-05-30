<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Verificar que se envió el formulario
if (!isset($_POST['producto_id']) || !isset($_POST['cantidad'])) {
    header("Location: registrar_venta.php?error=faltan_datos");
    exit();
}

$producto_id = (int) $_POST['producto_id'];
$cantidad_vendida = (int) $_POST['cantidad'];
$fecha_venta = date('Y-m-d');

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tienda_inventario");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener información del producto
$sql = "SELECT nombre, precio, cantidad FROM productos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$resultado = $stmt->get_result();
$producto = $resultado->fetch_assoc();

if (!$producto) {
    die("Producto no encontrado.");
}

if ($producto['cantidad'] < $cantidad_vendida) {
    die("Stock insuficiente para realizar la venta.");
}

// Calcular total
$total = $producto['precio'] * $cantidad_vendida;

// Registrar la venta
$sql_insert = "INSERT INTO ventas (producto_id, cantidad, total, fecha_venta) VALUES (?, ?, ?, ?)";
$stmt_insert = $conexion->prepare($sql_insert);
$stmt_insert->bind_param("iids", $producto_id, $cantidad_vendida, $total, $fecha_venta);
$stmt_insert->execute();

// Actualizar inventario
$sql_update = "UPDATE productos SET cantidad = cantidad - ? WHERE id = ?";
$stmt_update = $conexion->prepare($sql_update);
$stmt_update->bind_param("ii", $cantidad_vendida, $producto_id);
$stmt_update->execute();

header("Location: registrar_venta.php?mensaje=venta_registrada");
exit();
?>
