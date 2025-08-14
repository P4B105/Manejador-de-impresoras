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

require __DIR__ . '/db.php';

$sql = "SELECT * FROM impresora";
$result = $conexion->query($sql);
$printers = $result->fetch_all(MYSQLI_ASSOC);

$activas=$conexion->query("SELECT COUNT(`id_impresora`) as 'activas' from impresora where estado_actual like '1'; ");
$activas=$activas->fetch_assoc()['activas'];

$pendientes=$conexion->query("SELECT COUNT(`id_impresora`) as 'pendientes' from impresora where estado_actual like '2'; ");
$pendientes=$pendientes->fetch_assoc()['pendientes'];

$inactivas=$conexion->query("SELECT COUNT(`id_impresora`) as 'inactivas' from impresora where estado_actual like '3'; ");
$inactivas=$inactivas->fetch_assoc()['inactivas'];
$estadoArray = [
    '1' => 'activa',
    '2' => 'alerta',
    '3' => 'inactiva'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de administración impresoras</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
</head>
<body class="bg-gray-50 dashboard-body" >
    <?php 
        include 'encabezado.php';
    ?>
    <div class="container mx-auto px-4 py-8">
        <header class="mb-10">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 dashboard-h1">Panel de administración de impresoras</h1>
                    <p class="text-gray-600 dashboard-p">Supervise y administre todas las impresoras de su red</p>
                </div>
                <div>
                    <button id="refreshAll" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center dashboard-button">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 " viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                        Refrescar impresoras
                    </button>
                    <button id="refreshAll" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg flex items-center dashboard-button " style="margin-top: 10px;">
                        <a href="registrar_impresion.php">Agregar impresion al historial</a>
                    </button>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500">Impresoras totales</h3>
                    <p class="text-3xl font-semibold text-gray-800" id="totalPrinters"><?php echo count($printers); ?></p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500">Activa</h3>
                    <p class="text-3xl font-semibold text-green-600" id="onlinePrinters">
                        <?php echo $activas; ?>
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500">Alerta</h3>
                    <p class="text-3xl font-semibold text-yellow-600" id="warningPrinters">
                        <?php echo $pendientes; ?>
                    </p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-sm font-medium text-gray-500">Inactiva</h3>
                    <p class="text-3xl font-semibold text-red-600" id="offlinePrinters">
                        <?php echo $inactivas; ?>
                    </p>
                </div>
            </div>
        </header>

        <div class="mb-10" id="recommendations-section" style="display: none;">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center dashboard-h2">
                <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="h-6 w-6 mr-3 text-purple-600" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                Recomendaciones de IA
            </h2>
            <div id="recommendations-container" class="bg-purple-50 border-l-4 border-purple-500 text-purple-700 p-6 rounded-lg shadow">
                <p id="recommendations-placeholder">Generando recomendaciones...</p>
            </div>
        </div>


        <main>
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 dashboard-h2">Impresoras en el sistema</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="printerGrid">
                    <?php
                    foreach ($printers as $printer){
                        $statusClass = $printer['estado_actual'] === 'activa' ? 'activa' : 
                                    ($printer['estado_actual'] === 'inactiva' ? 'inactiva' : 'alerta');
                        $statusText = $printer['estado_actual'] === 'activa' ? 'activa' : 
                                    ($printer['estado_actual'] === 'inactiva' ? 'inactiva' : 'Necesita atención');
                    ?>
                    <div class="printer-card bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($printer['marca']) ?></h3>
                                <span class="inline-flex items-center <?= $printer['estado_actual'] === 'inactiva' ? 'text-gray-400' : '' ?>">
                                    <span class="status-indicator <?= $statusClass ?>"></span>
                                    <?= htmlspecialchars($estadoArray[$printer['estado_actual']]) ?>
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1"><?= htmlspecialchars($printer['modelo']) ?></p>
                            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($printer['ubicacion']) ?></p>
                        </div>
                        <div class="px-6 py-4">
                            <div class="mb-2">
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Toner Negro</span>
                                    <span class="text-sm <?= $printer['toner_negro'] <= 10 ? 'toner-critical' : ($printer['toner_negro'] <= 20 ? 'toner-low' : 'text-gray-700') ?>">
                                        <?= (int)$printer['toner_negro'] ?>%
                                    </span>
                                </div>
                                <div class="progress-bar bg-gray-200 rounded">
                                    <div class="h-full rounded <?= $printer['toner_negro'] <= 10 ? 'bg-red-500' : ($printer['toner_negro'] <= 20 ? 'bg-yellow-500' : 'bg-green-600') ?>" 
                                        style="width: <?= (int)$printer['toner_negro'] ?>%"></div>
                                </div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Toner Amarillo</span>
                                    <span class="text-sm <?= $printer['toner_amarillo'] <= 10 ? 'toner-critical' : ($printer['toner_amarillo'] <= 20 ? 'toner-low' : 'text-blue-500') ?>">
                                        <?= (int)$printer['toner_amarillo'] ?>%
                                    </span>
                                </div>
                                <div class="progress-bar bg-gray-200 rounded">
                                    <div class="h-full rounded <?= $printer['toner_amarillo'] <= 10 ? 'bg-red-500' : ($printer['toner_amarillo'] <= 20 ? 'bg-yellow-500' : 'bg-green-600') ?>" 
                                        style="width: <?= (int)$printer['toner_amarillo'] ?>%"></div>
                                </div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Toner Cian</span>
                                    <span class="text-sm <?= $printer['toner_cian'] <= 10 ? 'toner-critical' : ($printer['toner_cian'] <= 20 ? 'toner-low' : 'text-gray-700') ?>">
                                        <?= (int)$printer['toner_cian'] ?>%
                                    </span>
                                </div>
                                <div class="progress-bar bg-gray-200 rounded">
                                    <div class="h-full rounded <?= $printer['toner_cian'] <= 10 ? 'bg-red-500' : ($printer['toner_cian'] <= 20 ? 'bg-yellow-500' : 'bg-green-600') ?>" 
                                        style="width: <?= (int)$printer['toner_cian'] ?>%"></div>
                                </div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">Toner Magenta</span>
                                    <span class="text-sm <?= $printer['toner_magenta'] <= 10 ? 'toner-critical' : ($printer['toner_magenta'] <= 20 ? 'toner-low' : 'text-blue-500') ?>">
                                        <?= (int)$printer['toner_magenta'] ?>%
                                    </span>
                                </div>
                                <div class="progress-bar bg-gray-200 rounded">
                                    <div class="h-full rounded <?= $printer['toner_magenta'] <= 10 ? 'bg-red-500' : ($printer['toner_magenta'] <= 20 ? 'bg-yellow-500' : 'bg-green-600') ?>" 
                                        style="width: <?= (int)$printer['toner_magenta'] ?>%"></div>
                                </div>
                            </div>
                            <!-- Similar blocks for cyan, magenta, Toner Amarillos -->
                            <div class="mt-4 flex justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">cantidad de hojas:</span>
                                    <span class="text-sm text-gray-600"><?= number_format($printer['contador_paginas_actual']) ?></span>
                                </div>
                                <button class="text-xs bg-gray-100 hover:bg-gray-200 py-1 px-2 rounded">
                                                                        <a href="ver_historial.php?id=<?php echo htmlspecialchars($printer['id_impresora']); ?>">ver historial</a>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4 dashboard-h2">Historial de las Impresoras</h2>
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impresoras</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel Toner</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número Paginas</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ultima Actualización</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="statusTableBody">
                                            <?php 
                                            foreach ($printers as $printer) {
                                                $statusClass = $printer['estado_actual'] === 'activa' ? 'bg-green-100 text-green-800' : 
                                                            ($printer['estado_actual'] === 'inactiva' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800');
                                                $statusText = $printer['estado_actual'] === 'activa' ? 'activa' : 
                                                            ($printer['estado_actual'] === 'inactiva' ? 'inactiva' : 'Necesita atención');
                                            ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/0354bbf2-6ccd-4a2b-bf83-eafb8b101b7e.png" alt="Printer icon" class="h-10 w-10 rounded-full">
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($printer['marca']) ?></div>
                                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($printer['ubicacion']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                                        <?= $statusText ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        Black: <?= (int)$printer['toner_negro'] ?>% 
                                                        <span class="text-xs">|</span> C: <?= (int)$printer['toner_cian'] ?>% 
                                                        <span class="text-xs">|</span> M: <?= (int)$printer['toner_magenta'] ?>% 
                                                        <span class="text-xs">|</span> Y: <?= (int)$printer['toner_amarillo'] ?>%
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?= number_format($printer['contador_paginas_actual']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?= $printer['ultima_actualizacion'] ?>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="app.js" defer crossorigin="anonymous"></script>
</body>
</html>