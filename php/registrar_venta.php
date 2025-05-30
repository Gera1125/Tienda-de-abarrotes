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

// Obtener lista de productos con precio y cantidad
$resultado = $conexion->query("SELECT id, nombre, cantidad, precio FROM productos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registrar Venta</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Materialize CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <style>
    .cantidad-input {
      width: 70px;
      margin-left: 10px;
    }
    .producto-item {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }
    .producto-nombre {
      flex-grow: 1;
      margin-left: 10px;
    }
  </style>
</head>
<body>

<nav class="blue">
  <div class="nav-wrapper">
    <a href="#" class="brand-logo center">Registrar Venta</a>
  </div>
</nav>

<div class="container" style="margin-top: 40px;">
  <h5>Formulario de Venta</h5>
  <form method="POST" action="procesar_venta.php" id="formVenta">

    <div>
      <h6>Selecciona productos y cantidades</h6>
      <?php while ($producto = $resultado->fetch_assoc()): ?>
        <div class="producto-item">
          <label>
            <input type="checkbox" name="productos[]" value="<?= $producto['id'] ?>" data-precio="<?= $producto['precio'] ?>" data-stock="<?= $producto['cantidad'] ?>" class="producto-checkbox" />
            <span class="producto-nombre"><?= htmlspecialchars($producto['nombre']) ?> (Precio: $<?= number_format($producto['precio'],2) ?>, Stock: <?= $producto['cantidad'] ?>)</span>
          </label>
          <input 
            type="number" 
            name="cantidades[]" 
            min="1" 
            max="<?= $producto['cantidad'] ?>" 
            value="" 
            class="cantidad-input" 
            disabled
            placeholder="Cant." 
            />
        </div>
      <?php endwhile; ?>
    </div>

    <h6>Total de la venta: $<span id="totalVenta">0.00</span></h6>

    <button type="submit" class="btn green">Registrar Venta</button>
    <a href="../vendedor.html" class="btn grey">Cancelar</a>
  </form>
</div>

<!-- Scripts de Materialize y JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var elems = document.querySelectorAll('select');
    M.FormSelect.init(elems);

    const checkboxes = document.querySelectorAll('.producto-checkbox');
    const cantidades = document.querySelectorAll('.cantidad-input');
    const totalVenta = document.getElementById('totalVenta');
    const form = document.getElementById('formVenta');

    function actualizarTotal() {
      let total = 0;
      checkboxes.forEach((checkbox, i) => {
        if (checkbox.checked) {
          let cantidad = cantidades[i].value;
          // Si el campo está vacío o menor que 1, consideramos 1 para total pero validaremos al enviar
          cantidad = cantidad === "" || isNaN(cantidad) || cantidad < 1 ? 1 : parseInt(cantidad);
          const precio = parseFloat(checkbox.getAttribute('data-precio'));
          total += precio * cantidad;
        }
      });
      totalVenta.textContent = total.toFixed(2);
    }

    checkboxes.forEach((checkbox, i) => {
      checkbox.addEventListener('change', () => {
        cantidades[i].disabled = !checkbox.checked;
        if (!checkbox.checked) {
          cantidades[i].value = "";
        }
        actualizarTotal();
      });

      cantidades[i].addEventListener('input', () => {
        const max = parseInt(cantidades[i].max);
        let val = cantidades[i].value;

        // Validar max stock
        if (val !== "" && parseInt(val) > max) {
          cantidades[i].value = max;
          val = max;
        }
        // No forzar mínimo aquí para permitir borrar y escribir
        actualizarTotal();
      });

      // Validar al perder foco que no quede vacío ni menor a 1
      cantidades[i].addEventListener('blur', () => {
        if (checkboxes[i].checked) {
          let val = cantidades[i].value;
          if (val === "" || isNaN(val) || val < 1) {
            cantidades[i].value = 1;
          }
          actualizarTotal();
        }
      });
    });

    // Validación antes de enviar
    form.addEventListener('submit', (e) => {
      let productosSeleccionados = false;
      for(let i=0; i<checkboxes.length; i++) {
        if(checkboxes[i].checked) {
          productosSeleccionados = true;
          let val = cantidades[i].value;
          if(val === "" || isNaN(val) || val < 1) {
            alert('Por favor ingresa una cantidad válida para el producto: ' + cantidades[i].previousElementSibling.innerText);
            e.preventDefault();
            return;
          }
        }
      }
      if(!productosSeleccionados) {
        alert('Debes seleccionar al menos un producto para registrar la venta.');
        e.preventDefault();
      }
    });

  });
</script>

</body>
</html>