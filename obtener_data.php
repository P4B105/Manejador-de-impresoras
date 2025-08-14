<?php
require 'db.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT 
                id_impresora AS id, 
                marca AS name, 
                modelo AS model, 
                ubicacion AS location, 
                estado_actual AS status, 
                toner_negro AS toner_black, 
                toner_cian AS toner_cyan, 
                toner_magenta AS toner_magenta, 
                toner_amarillo AS toner_yellow, 
                contador_paginas_actual AS page_count
            FROM impresora";
    
    $result = $conexion->query($sql);


    if ($result === false) {
        throw new Exception("Error en la consulta SQL: " . $conexion->error);
    }

    $printers = [];

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $printers[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'model' => $row['model'],
                'location' => $row['location'],
                'status' => $row['status'],
                'toner' => [
                    'black' => (int)$row['toner_black'],
                    'cyan' => (int)$row['toner_cyan'],
                    'magenta' => (int)$row['toner_magenta'],
                    'yellow' => (int)$row['toner_yellow']
                ],
                'pageCount' => (int)$row['page_count'],
            ];
        }
    }

    echo json_encode($printers);
    
} catch (Exception $e) {
    http_response_code(500);

    echo json_encode(['error' => $e->getMessage()]);
}

$conexion->close();
?>