<?php

session_start();

// Verifica si el usuario NO ha iniciado sesión
if (!isset($_SESSION['username'])){ 
    // Redirige al usuario a la página de inicio de sesión
    header("Location: login.php");
    exit; // Termina el script para asegurar la redirección
}

// **IMPORTANTE**: Encabezados para evitar el caché en todas las páginas protegidas
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Fecha en el pasado


include 'db.php';

$usuarios_result = $conexion->query("SELECT id_usuario, alias FROM usuario ORDER BY alias");
if (!$usuarios_result) {
    die("Error al cargar usuarios: " . $conexion->error);
}

// Cargar impresoras
$impresoras_result = $conexion->query("SELECT id_impresora, marca, modelo FROM impresora ORDER BY marca, modelo");
if (!$impresoras_result) {
    die("Error al cargar impresoras: " . $conexion->error);
}


$mensaje = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se reciben los IDs numéricos del formulario
    $id_usuario = (int)($_POST['id_usuario'] ?? 0);
    $id_impresora = (int)($_POST['id_impresora'] ?? 0);
    $cantidad_hojas = (int)($_POST['cantidad_hojas'] ?? 0);

    if ($id_usuario > 0 && $id_impresora > 0 && $cantidad_hojas > 0) {
        $fecha_impresion = date("Y-m-d H:i:s");


        $sql = "INSERT INTO historial_impresiones (id_usuario, id_impresora, cantidad_hojas, fecha) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);


        $stmt->bind_param("iiis", $id_usuario, $id_impresora, $cantidad_hojas, $fecha_impresion);

        if ($stmt->execute()) {
            $mensaje = "✅ Impresión registrada correctamente en la base de datos.";
        } else {
            $mensaje = "❌ Error al registrar la impresión: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Impresión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .container{
        max-width: 600px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
        }
        h2{
            text-align: center; margin-bottom: 25px;
        }
        label{ display: block; margin-bottom: 8px; font-weight: bold;
        }
        select, input[type="text"], input[type="number"]{ 
            width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 6px; border: 2px solid transparent; background-color: var(--color-gris-claro;) }
        button{ 
            background: var(--color-azul-medianoche)!important; color: white; padding: 12px 20px; border: 2px solid transparent; border-radius: 6px; width: 100%; font-size: 16px; cursor: pointer; 
            margin-top: 5%;
        }
        button:hover{ 
            background: var(--color-azul-medianoche);
            text-shadow: 0 0 10px white;
        }
        .mensaje{ 
            margin-top: 15px; text-align: center; font-weight: bold; padding: 10px; border-radius: 5px;
        }
        .mensaje.exito{ 
            color: #155724; background-color: #d4edda; border-color: #c3e6cb;
        }
        .mensaje.error{ 
            color: #721c24; background-color: #f8d7da; border-color: #f5c6cb;
        }
        .footer{ 
            text-align: center; font-size: 13px; color: #999; margin-top: 25px;
        }
    </style>
</head>
<body class="registrar_impresion-body">

<?php 
include "encabezado.php";
?>
    <div class="container registrar_impresion-div">
        <h2>Registrar Impresión</h2>

        <form method="post">
            <label for="id_usuario">Usuario:</label>
            <select name="id_usuario" id="id_usuario" class="registrar_impresora-select" required>
                <option value="">Seleccionar usuario</option>
                <?php while($u = $usuarios_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($u['id_usuario']) ?>"><?= htmlspecialchars($u['alias']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="id_impresora">Impresora:</label>
            <select name="id_impresora" id="id_impresora" class="registrar_impresora-select" required>
                <option value="">Seleccionar impresora</option>
                <?php while($i = $impresoras_result->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($i['id_impresora']) ?>">
                        <?= htmlspecialchars($i['marca'] . ' - ' . $i['modelo']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="cantidad_hojas">Cantidad de hojas:</label>
            <input type="number" name="cantidad_hojas" id="cantidad_hojas" min="1" class="registrar_impresora-input" placeholder="Ingrese la cantidad de hojas impresas" required>

            <button type="submit">Registrar Impresión</button>
        </form>

        <?php
        if (!empty($mensaje)) {
            $claseMensaje = strpos($mensaje, '✅') !== false ? 'exito' : 'error';
            echo "<div class='mensaje $claseMensaje'>$mensaje</div>";
        }
        ?>
    </div>
</body>
</html>