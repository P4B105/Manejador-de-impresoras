<?php  include 'menu.php'; 

$archivoUsuarios = 'usuarios.json';
$usuarios = file_exists($archivoUsuarios) ? json_decode(file_get_contents($archivoUsuarios), true) : [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre']);

    if (!empty($nombre)) {
        $usuarios[] = $nombre;
        file_put_contents($archivoUsuarios, json_encode($usuarios, JSON_PRETTY_PRINT));
        $mensaje = "✅ Usuario registrado correctamente.";
    } else {
        $mensaje = "⚠️ El nombre no puede estar vacío.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #eef1f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 10px;
            width: 90%;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .mensaje {
            margin-top: 15px;
            color: green;
        }

        .lista {
            margin-top: 20px;
            font-size: 14px;
        }

        .lista span {
            display: inline-block;
            background: #f2f2f2;
            margin: 4px;
            padding: 6px 12px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>👤 Registrar Usuario</h2>
        <form method="post">
            <input type="text" name="nombre" placeholder="Nombre del usuario">
            <br>
            <button type="submit">Registrar</button>
        </form>

        <?php if (!empty($mensaje)) echo "<div class='mensaje'>$mensaje</div>"; ?>

        <?php if (!empty($usuarios)): ?>
            <div class="lista">
                <strong>Usuarios registrados:</strong><br>
                <?php foreach ($usuarios as $usuario): ?>
                    <span><?= htmlspecialchars($usuario) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
