<?php 
function sendTokkoWebContact(array $contactData, string $apiKey): ?array {
    if (empty($apiKey)) {
        error_log('API key de Tokko no está definida.');
        return ['error' => true, 'message' => 'API key no definida'];
    }

    $BASE_SEND_URL = "https://tokkobroker.com/api/v1/webcontact/?key=";
    $url = $BASE_SEND_URL . urlencode($apiKey);

    $content = json_encode($contactData);
    if ($content === false) {
        error_log('Error al convertir contactData a JSON: ' . json_last_error_msg());
        return ['error' => true, 'message' => 'Error al codificar los datos'];
    }

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

    $json_response = curl_exec($curl);

    if ($json_response === false) {
        error_log('Curl error: ' . curl_error($curl));
        curl_close($curl);
        return ['error' => true, 'message' => 'Error de conexión con Tokko'];
    }

    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    // Log para debug
    error_log("Tokko API response (HTTP $status): " . var_export($json_response, true));

    if ($status != 201) {
        error_log("Tokko API error (HTTP $status): " . $json_response);
        return [
            'error' => true,
            'message' => 'Error en la respuesta de la API de Tokko',
            'status' => $status,
            'response' => $json_response
        ];
    }

    // Si está todo OK pero no devuelve contenido
    if (empty($json_response)) {
        return [
            'success' => true,
            'message' => 'Mensaje enviado con éxito.'
        ];
    }

    $result = json_decode($json_response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('Error al decodificar respuesta de Tokko: ' . json_last_error_msg());
        return ['error' => true, 'message' => 'Respuesta inválida de la API de Tokko'];
    }

    return $result;
}

function saveDataGoogleSheet(array $data, string $url): ?string {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($data), // convierte array a form-urlencoded
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
        ],
    ]);

    $response = curl_exec($curl);

    if ($response === false) {
        error_log('Curl error: ' . curl_error($curl));
        curl_close($curl);
        return null;
    }

    curl_close($curl);
    return $response;
}