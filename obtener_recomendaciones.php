<?php

require 'db.php';

$apiKey = 'AIzaSyDBZXrKyqXSAfK_33ZDuTYD6iQdfjmTPbw'; 
header('Content-Type: application/json');


$printersDataJson = file_get_contents('php://input');


error_log("Received printer data JSON: " . $printersDataJson);


$printersData = json_decode($printersDataJson, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid JSON input received', 'json_error' => json_last_error_msg()]);
    exit;
}

error_log("Decoded printer data: " . print_r($printersData, true));

if (empty($printersData)) {

    echo json_encode([]); 
    exit;
}

$model = 'gemini-2.5-flash';
$url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . trim($apiKey); 

$prompt = "
Eres un asistente experto en mantenimiento de TI especializado en impresoras.
Analiza el siguiente listado de impresoras en formato JSON y proporciona recomendaciones claras y accionables para cada una.
Considera los siguientes criterios para tus recomendaciones:
- Nivel de tóner por debajo del 30%: Recomendar 'Pedir tóner de reemplazo'.
- Nivel de tóner por debajo del 10%: Recomendar 'Reemplazo de tóner urgente'.
- Estado 'alerta', 'Necesita atención' o 'pendiente': Recomendar 'Revisar impresora en busca de errores (atasco de papel, etc.)'
- Estado 'inactiva' o '3' (si es 3, no mencionarlo y decir en su lugar 'inactiva'): Recomender 'Verificar conexión de red y alimentación eléctrica'.
- Contador de páginas muy alto (ej. > 200 páginas): Recomender 'Verificar la cantidad de hojas de la impresora'.
- contador de paginas muy bajo (ej. < 10 páginas): Recomendar 'Ponerle mas hojas a la impresora antes de que se vacíe'.
- contador de páginas vacío: recomendar 'Verificar el contador de páginas, puede que no se haya actualizado correctamente'.
Evita mencionar los numeros recibidos en los estados cuando contestes. Devuelve tu respuesta únicamente en formato JSON, como un array de objetos. Cada objeto debe tener 'id' (el id de la impresora) y 'recommendation' (una cadena de texto con tu recomendación la cual debe tener en cuenta errores comunes de la marca de la impresora).
Asegúrate de que la salida sea un JSON válido y bien formado, sin texto adicional antes o después del JSON.
Datos de las impresoras:
" . json_encode($printersData); 

error_log("Full Gemini prompt: " . $prompt);

$data = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
curl_setopt($ch, CURLOPT_TIMEOUT, 30); 

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log cURL details
error_log("cURL HTTP Code: " . $httpCode);
error_log("cURL Error: " . $curlError);
error_log("Gemini Raw Response: " . $response);


if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al conectar con la API de Gemini: ' . $curlError]);
    exit;
}

$responseData = json_decode($response, true);

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['error' => 'Error de la API de Gemini', 'details' => $responseData, 'http_code' => $httpCode]);
    exit;
}


if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $generatedText = $responseData['candidates'][0]['content']['parts'][0]['text'];
    error_log("Gemini Generated Text: " . $generatedText);


    $cleanedText = preg_replace('/```(?:json)?\s*([\s\S]*?)\s*```/', '$1', $generatedText);
    error_log("Cleaned Text (after markdown removal): " . $cleanedText);

    $recommendations = json_decode($cleanedText, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        
        http_response_code(500);
        echo json_encode(['error' => 'Gemini did not return valid JSON recommendations', 'gemini_raw_text' => $generatedText, 'json_error' => json_last_error_msg()]);
        exit;
    }

    if (!is_array($recommendations)) {
        
        $recommendations = [];
        error_log("Gemini response was not an array, defaulting to empty array.");
    }
    echo json_encode($recommendations);

} else {
    
    http_response_code(500);
    echo json_encode(['error' => 'No content found in Gemini API response', 'full_response' => $responseData]);
    exit;
}

?>