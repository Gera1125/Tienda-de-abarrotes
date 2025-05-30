<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Validar que se haya recibido el ID del producto
if (!isset($_GET['id'])) {
    echo "❌ ID de producto no proporcionado.";
    exit();
}

$id = intval($_GET['id']);

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "tienda_inventario");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Eliminar producto
$sql = "DELETE FROM productos WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Redirigir al inventario con un mensaje
    header("Location: inventario.php?mensaje=eliminado");
} else {
    echo "❌ Error al eliminar el producto.";
}

$stmt->close();
$conexion->close();
?>
