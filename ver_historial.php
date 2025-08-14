<?php
include 'verificarSesion.php';
include 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de impresora no especificado o inválido.");
}
$id_impresora = (int)$_GET['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de impresora no especificado o inválido.");
}
$id_impresora = (int)$_GET['id'];

// --- 2. OBTENER EL NOMBRE DE LA IMPRESORA PARA LOS TÍTULOS ---
$printer_name = 'Impresora Desconocida';
$stmt_info = $conexion->prepare("SELECT marca, modelo FROM impresora WHERE id_impresora = ?");
$stmt_info->bind_param("i", $id_impresora);
if ($stmt_info->execute()) {
    $result_info = $stmt_info->get_result();
    if ($printer_info = $result_info->fetch_assoc()) {
        $printer_name = htmlspecialchars($printer_info['marca'] . ' - ' . $printer_info['modelo']);
    }
}
$stmt_info->close();

// --- 3. CONSULTA SQL PARA ESTADÍSTICAS (CON WHERE) ---
$sql_stats = "SELECT
                u.alias,
                SUM(hi.cantidad_hojas) AS total_hojas
            FROM
                historial_impresiones hi
            JOIN
                usuario u ON hi.id_usuario = u.id_usuario
            WHERE
                hi.id_impresora = ? -- Filtro por ID de impresora
            GROUP BY
                u.alias
            ORDER BY
                total_hojas DESC;";

$stmt_stats = $conexion->prepare($sql_stats);
$stmt_stats->bind_param("i", $id_impresora);
$stmt_stats->execute();
$result_stats = $stmt_stats->get_result();

// Preparar los datos para el gráfico
$usuarios_labels = [];
$hojas_data = [];
while ($row = $result_stats->fetch_assoc()) {
    $usuarios_labels[] = $row['alias'];
    $hojas_data[] = (int)$row['total_hojas'];
}
$stmt_stats->close();


// --- 4. CONSULTA SQL PARA EL HISTORIAL DETALLADO (CON WHERE) ---
$sql_historial = "SELECT
                    hi.fecha,
                    hi.cantidad_hojas AS hojas,
                    i.marca,
                    i.modelo,
                    u.alias
                FROM
                    historial_impresiones hi
                JOIN
                    impresora i ON hi.id_impresora = i.id_impresora
                JOIN
                    usuario u ON hi.id_usuario = u.id_usuario
                WHERE
                    hi.id_impresora = ? -- Filtro por ID de impresora
                ORDER BY
                    hi.fecha DESC;";

$stmt_historial = $conexion->prepare($sql_historial);
$stmt_historial->bind_param("i", $id_impresora);
$stmt_historial->execute();
$result_historial = $stmt_historial->get_result();

$stmt_historial->close();
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial y Estadísticas de Impresiones</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #eef1f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        /* Estilo para el contenedor del gráfico */
        .chart-container {
            position: relative;
            margin: auto;
            margin-bottom: 40px; /* Espacio entre el gráfico y la tabla */
            height: 40vh;
            width: 80vw;
            max-width: 800px;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 14px; text-align: center; font-size: 15px; }
        th { background-color: #1A2B42; color: white; text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; }
        tr:nth-child(even) { background-color: #f5f5f5; }
        tr:hover { background-color: #e3f0ff; }
        .footer { text-align: center; font-size: 12px; color: #999; margin-top: 25px; }
        .impresora { font-style: italic; color: #555; font-size: 13px; }
    </style>
</head>
<body>
    <?php 
        include 'encabezado.php';
    ?>
    <!-- boton de Imprimir -->
    <?php if ($result_historial->num_rows > 0): ?>
        <div class="container"> 
            <header class="mb-10">
                <div class="flex justify-between">
                    <div></div>
                    <form id="formImprimir" <?php echo 'action="crearPDF.php?id='.$id_impresora.'"';?> method="post">
                        <input type="hidden" name="chartImage1" id="chartImage1">
                        <input type="hidden" name="chartImage2" id="chartImage2">
                        <input type="submit" id="refreshAll" value="Imprimir registro" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center dashboard-button ">
                    </form>
                </div>
        </div>
    <?php endif; ?>

    <div class="container">
        <h2>Estadísticas de Impresión por Usuario</h2>

        <div class="chart-container">
            <canvas id="usoPorUsuarioChart"></canvas>
        </div>
        <div class="chart-container2">
            <canvas id="usoPorUsuarioChartPie"></canvas>
        </div>

        <h2>Historial de Impresiones</h2>
        <?php if ($result_historial->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Impresora (Marca - Modelo)</th>
                        <th>Hojas</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($registro = $result_historial->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($registro['alias']) ?></td>
                            <td class="impresora"><?= htmlspecialchars($registro['marca']) ?> - <?= htmlspecialchars($registro['modelo']) ?></td>
                            <td><?= (int)$registro['hojas'] ?></td>
                            <td><?= date('d/m/Y', strtotime($registro['fecha'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="text-align: center; color: #666;">No hay registros de impresiones en el historial.</p>
        <?php endif; ?>
    </div>

    <script>
        const ctx = document.getElementById('usoPorUsuarioChart');
        const ctxPie = document.getElementById('usoPorUsuarioChartPie');
        
        const usuarios = <?php echo json_encode($usuarios_labels); ?>;
        const hojas = <?php echo json_encode($hojas_data); ?>;

        const backgroundColors = [];
        const borderColors = [];

        // Por cada usuario en el gráfico, genera un color RGBA aleatorio.
        for (let i = 0; i < usuarios.length; i++) {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            backgroundColors.push(`rgba(${r}, ${g}, ${b}, 0.6)`); // Color de fondo con transparencia
            borderColors.push(`rgba(${r}, ${g}, ${b}, 1)`);      // Color de borde sólido
        }

        new Chart(ctx, {
            type: 'bar', //posibles tipos: 'bar', 'line', 'pie', doughtnut.
            data: {
                labels: usuarios,
                datasets: [{
                    label: 'Hojas Impresas',
                    data: hojas,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Hojas'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Usuarios'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false 
                    },
                    title: {
                        display: true,
                        text: 'Total de Hojas Impresas por Usuario'
                    }
                }
            }
        });
        new Chart(ctxPie, {
            type: 'pie', 
            data: {
                labels: usuarios,
                datasets: [{
                    label: 'Hojas Impresas',
                    data: hojas,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Hojas'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Usuarios'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false 
                    },
                    title: {
                        display: true,
                        text: 'Total de Hojas Impresas por Usuario'
                    }
                }
            }
        });

    </script>
    <script>
        document.getElementById("formImprimir").addEventListener("submit", function(event) {
            document.getElementById("chartImage1").value = ctx.toDataURL("image/png");
            document.getElementById("chartImage2").value = ctxPie.toDataURL("image/png");
        });
    </script>
</body>
</html>