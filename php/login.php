<?php
session_start(); // Iniciar sesión

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "tienda_inventario");

    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Recibir datos del formulario y evitar warnings si no existen
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $contraseña = isset($_POST['contraseña']) ? $_POST['contraseña'] : '';

    // Preparar consulta para evitar inyección SQL
    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();

        // Para mejorar la seguridad: si usas contraseñas en texto plano (no recomendado), compara directamente
        // Si usas hash, utiliza password_verify()
        if ($fila['contraseña'] === $contraseña) {
            $_SESSION['usuario'] = $fila['usuario'];
            $_SESSION['nivel'] = $fila['nivel'];

            // Redireccionar según el nivel
            if ($fila['nivel'] === 'administrador') {
                header("Location: ../admin.html");
                exit();
            } elseif ($fila['nivel'] === 'vendedor') {
                header("Location: ../vendedor.html");
                exit();
            } else {
                echo "Nivel de usuario no reconocido.";
            }
        } else {
            echo "⚠️ Contraseña incorrecta.";
        }
    } else {
        echo "⚠️ Usuario no encontrado.";
    }

    $stmt->close();
    $conexion->close();

} else {
    // Si se intenta acceder a este archivo sin enviar datos del formulario, redirigir a index.html
    header("Location: ../index.html");
    exit();
}
?>