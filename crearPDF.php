<?php
include 'db.php';

if (isset($_POST['chartImage1']) && isset($_POST['chartImage2'])) {
    $img1 = $_POST['chartImage1'];
    $img2 = $_POST['chartImage2'];//base64 string
}else{
    die("Error en el form!");
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Crear pdf</title>
</head>
<body>

    <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL); //esto no es necesario, es para ver si sale algun error

        $path = 'Imagenes/logoNegro.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $logo64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        require 'autoload.inc.php';

        use Dompdf\Dompdf;
        $dompdf = new Dompdf();

        //-----cargar html-----
        //scripts y links de estilo
        $html = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">';

        // css
        $html = $html.'<style>
        body { font-family: "Segoe UI", sans-serif; padding: 20px; }
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
        .page_break { page-break-before: always; }
        </style>';

        
        //ayuda porfavor saquenme de php

        $html = $html.'
            <img src="'.$logo64.'" style="width:15%; padding-top:-80px;">
        ';

        $html = $html.'
            <div class="container">
        <h2>Estadísticas de Impresión por Usuario</h2>';
        
        $html= $html.'
            <img src="' . $img1 . '" style="width: 100%;"><br><br><br><br><br>

            <img src="' . $img2 . '" style="width: 100%;">
        ';

        $html=$html.'<div class="page_break"></div>'; //salto de linea

        $html = $html.'
            <img src="'.$logo64.'" style="width:15%; padding-top:-80px;">
        ';

        $html=$html.'
        <h2>Historial de Impresiones</h2>
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
                    ';
                    
                    $concatenar='';

                    while($registro = $result_historial->fetch_assoc()){
                        $concatenar=$concatenar.'<tr>
                            <td>'.htmlspecialchars($registro["alias"]).'</td>
                            <td class="impresora">'.htmlspecialchars($registro["marca"]).'-'.htmlspecialchars($registro["modelo"]).'</td>
                            <td>'.(int)$registro["hojas"].'</td>
                            <td>'.date("d/m/Y", strtotime($registro['fecha'])).'</td>
                        </tr>';
                    }
                    $html=$html.$concatenar.'
                </tbody>
            </table>
    </div>
        ';


        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        ob_end_clean(); // limpia el html
        $dompdf->stream("Registro Impresion.pdf", ["Attachment" => false]); // true=forzar descarga

    ?>


</body>
</html>