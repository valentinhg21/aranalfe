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

function export_all_properties_to_google_sheet() {
    $all = get_all_property_by_filter([], 400);
    $propertys = $all['objects'] ?? [];

    $drive_url = 'https://script.google.com/macros/s/AKfycbzYNPktlCX4mr6CRrMWa8Tc45JCb7fPkH5H-ctom2ds7ov2p_3-O40SHdH5Tf9KM9-V/exec';
    $all_data = [];

    foreach ($propertys as $property) {
        $direccion = $property['address'] ?? '';
        $id_propiedad = $property['id'] ?? '';
        $metros_cubiertos = $property['roofed_surface'] ?? '';
        $metros_totales = $property['total_surface'] ?? '';
        $tipo_de_propiedad = $property['type']['name'] ?? '';
        $estado = get_status_property($property['status'] ?? '');
        $barrio = $property['location']['name'] ?? '';
        $frente = $property['front_measure'] ?? '';
        $fondo = $property['depth_measure'] ?? '';
        $count_prices = count($property['operations'] ?? []);
        $currency_price = $property['operations'][0]['prices'][0]['currency'] ?? '';
        $price = $property['operations'][0]['prices'][0]['price'] ?? 0;
        $total_price = render_price_format($price, $currency_price);
        $total_price_alquiler = '';
        if ($count_prices > 1) {
            $total_price_alquiler = render_price_format(
                $property['operations'][1]['prices'][0]['price'] ?? 0,
                $property['operations'][1]['prices'][0]['currency'] ?? ''
            );
        }
        $expensas = render_price_format($property['expenses'] ?? 0, 'ARS');
        $ambientes = $property['room_amount'] ?? '';
        $descripcion = preg_replace('/<p>\s*(<br\s*\/?>)?\s*<\/p>/i', '', $property['description'] ?? '');
        $antiguedad = $property['age'] ?? null;
        $condicion = $property['property_condition'] ?? '';
        $orientacion = $property['orientation'] ?? '';
        $propietario = $property['internal_data']['property_owners'][0]['name'] ?? '';
        $propietario_id = $property['internal_data']['property_owners'][0]['id'] ?? '';
        $propietario_telefono = $property['internal_data']['property_owners'][0]['cellphone'] ?? 'No esta cargado';

        $age_text = '';
        if ($antiguedad !== null) {
            switch ($antiguedad) {
                case 0: $age_text = 'A estrenar'; break;
                case -1: $age_text = 'En construcción'; break;
                case 1: $age_text = '1 año'; break;
                default:
                    if ($antiguedad > 1) {
                        $age_text = $antiguedad . ' años';
                    }
                    break;
            }
        }

        $all_data[] = [
            'direccion'         => $direccion,
            'id_propiedad'      => $id_propiedad,
            'metros_cubiertos'  => $metros_cubiertos,
            'metros_totales'    => $metros_totales,
            'tipo_de_propiedad' => $tipo_de_propiedad,
            'estado'            => $estado,
            'barrio'            => $barrio,
            'frente'            => $frente,
            'fondo'             => $fondo,
            'precio_venta'      => $total_price,
            'precio_alquiler'   => $total_price_alquiler,
            'expensas'          => $expensas,
            'ambientes'         => $ambientes,
            'descripcion'       => $descripcion,
            'antiguedad'        => $age_text,
            'condicion'         => $condicion,
            'orientacion'       => $orientacion,
            'propietario'       => $propietario,
            'id_propietario'    => $propietario_id,
            'telefono'          => $propietario_telefono,
        ];
    }

    // Enviar TODO el lote
    $response = saveDataGoogleSheet(['data' => json_encode($all_data)], $drive_url);
    echo $response;
}