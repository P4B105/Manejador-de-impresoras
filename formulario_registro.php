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

    require 'db.php';

    $query=$conexion->query("SELECT id_usuario as id, alias, nombre, apellido FROM usuario;");

    if ($query === false) {
        throw new Exception("Error en la consulta SQL: " . $conexion->error);
    }
    $usrs = [];

    if ($query->num_rows > 0) {
        while($row = $query->fetch_assoc()) {
            $usrs[] = [
                'alias' => $row['alias'],
                'nombre' => $row['nombre'],
                'apellido' => $row['apellido'],
                'id' => $row['id']
            ];
        }
    }

    $usuarios=array();
    $ids=array();

    foreach($usrs as $u){
        $usuarios[]=$u['apellido']." ".$u['nombre']." (".$u['alias'].")";
        $ids[]=$u['id']; //que por qué las puse en arrays diferentes? PORQUE ESTOY LOCOOOO!!!!!!!1
    }

    $query=$conexion->query("SELECT id_impresora as id, marca, modelo FROM impresora;");
    if ($query === false) {
        throw new Exception("Error en la consulta SQL: " . $conexion->error);
    }

    $imps = [];
    if ($query->num_rows > 0) {
        while($row = $query->fetch_assoc()) {
            $imps[] = [
                'marca' => $row['marca'],
                'modelo' => $row['modelo'],
                'id' => $row['id']
            ];
        }
    }

    $impresoras=array();
    $idImps=array();

    foreach($imps as $u){
        $impresoras[]=$u['marca']."-".$u['modelo'];
        $idImps[]=$u['id'];
    }
    
    $conexion->close();
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="formulario_registro-body">
    <?php
        include 'encabezado.php';
    ?>
    <form action="insertar_impresion.php" method="post" class="formulario_registro-form">
        <h2 class="formulario_registro-h2">Registrar Impresión</h2>
        <div class="formulario_registro-div-usuario">
            <label for="usuario" class="formulario_registro-label">Usuario:</label>
            <select name="usuario" id="usuario" class="formulario_registro-select" required>
                <option value="">Seleccionar usuario</option>
                <?php for($i=0;$i<count($usuarios);$i++): ?>
                <option value="<?= htmlspecialchars($ids[$i]) ?>"><?= htmlspecialchars($usuarios[$i]) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="formulario_registro-div-cantidad-hojas">
            <label for="cantidad_hojas" class="formulario_registro-label">Cantidad de hojas:</label>
            <input type="number" placeholder="Ingrese la cantidad de hojas impresas" name="cantidad_hojas" id="cantidad_hojas" min="1" class="formulario_registro-input" required>
        </div>
        <div class="formulario_registro-div-impresora">
            <label for="impresora" class="formulario_registro-label">Nombre de la impresora:</label>
            <select name="impresora" id="impresora" class="formulario_registro-select" required>
                <option value="" class="formulario_registro-option">Seleccionar impresora</option>
                <?php for($i=0;$i<count($impresoras);$i++): ?>
                <option class="formulario_registro-option" value="<?= htmlspecialchars($idImps[$i]) ?>"><?= htmlspecialchars($impresoras[$i]) ?></option>
            <?php endfor; ?>
        </select>
        </div>
        <input type="submit" value="Registrar" class="formulario_registro-submit"></input>
    </form>
    <?php if (!empty($mensaje)) echo "<div class='mensaje'>$mensaje</div>"; ?>

</body>
</html>
