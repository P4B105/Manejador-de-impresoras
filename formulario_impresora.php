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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Impresoras</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 formulario_impresora-body">  
    <?php 
        include 'encabezado.php'
    ?>
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 formulario_impresora-h1">Registro de Impresoras</h1>
        
        <!-- Form Container -->
        <form id="printerForm" action="procesar_impresora.php" method="POST" class="form-container bg-white rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-6 border-b pb-2 formulario_impresora-h2">Detalles de la Impresora</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="serialNumber" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Número de Serie</label>
                    <input type="text" placeholder="Ingrese el numero de serie" id="serialNumber" name="serialNumber" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Marca</label>
                    <input type="text" placeholder="Ingrese la marca" id="brand" name="brand" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Modelo</label>
                    <input type="text" placeholder="Ingrese le modelo" id="model" name="model" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="printerType" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Tipo de Impresora</label>
                    <select id="printerType" name="printerType" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                        <option value="">Selecione el tipo</option>
                        <option value="1">Inyección de Tinta</option>
                        <option value="2">3D</option>
                        <option value="3">Térmica</option>
                        <option value="4">Láser</option>
                        <option value="5">Multifunción</option>
                        <option value="6">Plotters</option>
                        <option value="7">Tanque de Tinta</option>
                        <option value="8">De Impacto</option>
                        <option value="9">Otro</option>
                    </select>
                </div>
                
                <div>
                    <label for="serviceDate" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Fecha de Puesta en Servicio</label>
                    <input type="date" id="serviceDate" name="serviceDate" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="pageCounter" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Contador de Páginas Actual</label>
                    <input type="number" placeholder="Ingrese el numero de paginas que se han imprimido" id="pageCounter" name="pageCounter" min="0" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="tonerBlack" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Nivel de Tóner Negro (%)</label>
                    <input type="number" placeholder="Ingrese la cantidad de toner negro" id="tonerBlack" name="tonerBlack" min="0" max="100" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="tonerCyan" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Nivel de Tóner Cian (%)</label>
                    <input type="number" placeholder="Ingrese la cantidad de toner cian" id="tonerCyan" name="tonerCyan" min="0" max="100" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="tonerMagenta" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Nivel de Tóner Magenta (%)</label>
                    <input type="number" placeholder="Ingrese la cantidad de toner magenta" id="tonerMagenta" name="tonerMagenta" min="0" max="100" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="tonerYellow" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Nivel de Tóner Amarillo (%)</label>
                    <input type="number" placeholder="Ingrese la cantidad de toner amarillo" id="tonerYellow" name="tonerYellow" min="0" max="100" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Ubicación de la Impresora</label>
                    <input type="text" placeholder="Ingrese el lugar en el que esta instalada la impresora" id="location" name="location" required maxlength="255"
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                </div>

                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1 formulario_impresora-label">Estado de la impresora</label>
                    <select id="estado" name="estado" required
                        class="w-full px-3 py-2 border border-gray rounded-md [font-size:0.85rem] [height:3rem] focus:outline-none focus:outline-offset-[-1px] focus:![outline:2px_solid_#1A2B42] placeholder:[font-size:0.85rem] placeholder:text-[#5A7B9B] formulario_impresora-input">
                        <option value="">Selecione el estado</option>
                        <option value="1">Activa</option>
                        <option value="2">Pendiente</option>
                        <option value="3">Inactiva</option>
                    </select>
                </div>

            </div>
            
            <!-- Form Actions -->
            <div class="mt-8 flex justify-end space-x-3">
                <button type="reset" class="px-4 py-2 border border-solid border-[2.5px] border-[#1A5276] rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none formulario_impresora-button-limpiar">
                    Limpiar
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-offset-2 formulario_impresora-button-registrar">
                    Registrar Impresora
                </button>
            </div>
        </form>
    </div>
</body>

<script src="verificacion_form_impresora.js"> </script>
</html>