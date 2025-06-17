<?php 
// function get_all_property_by_filter(array $params = [], int $limit = 12, int $offset = 0, string $order_by = "created_date", string $order = 'ASC'): array {
//     $config = require get_template_directory() . '/tokko-api/config.php';

//     if (!isset($params['data'])) {
//         $params['data'] = [
//             'current_localization_id' => 1,
//             'current_localization_type' => 'country',
//             'price_from' => 1,
//             'price_to' => 999999999,
//             'operation_types' => [1, 2],
//             'property_types' => range(1, 28),
//             'currency' => 'ANY',
//             'filters' => []
//         ];
//     }

//     // Codificar el JSON de 'data'
//     $data_json = json_encode($params['data']);

//     // Construir la URL en orden manualmente
//     $url = $config['property_search_url'] . '?' . http_build_query([
//         'lang'     => 'es_ar',
//         'format'   => 'json',
//         'limit'    => $limit,
//         'offset'   => $offset,
//         'order_by' => $order_by,
//         'order'    => $order,
//         'data'     => $data_json,
//         'key'      => $config['api_token'],
//     ]);

//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

//     $response = curl_exec($ch);

//     if ($response === false) {
//         echo 'Curl error: ' . curl_error($ch);
//         curl_close($ch);
//         return [];
//     }

//     curl_close($ch);
//     $decoded = json_decode($response, true);

//     if (json_last_error() !== JSON_ERROR_NONE) {
//         echo 'JSON decode error: ' . json_last_error_msg();
//         return [];
//     }
  
//     return $decoded ?: [];
// }

function get_all_property_by_filter(array $params = [], int $limit = 12, int $offset = 0, string $order_by = "created_date", string $order = 'ASC'): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    if (!isset($params['data'])) {
        $params['data'] = [
            'current_localization_id' => 1,
            'current_localization_type' => 'country',
            'price_from' => 1,
            'price_to' => 999999999,
            'operation_types' => [1, 2],
            'property_types' => range(1, 28),
            'currency' => 'ANY',
            'filters' => [],
            'with_tags' => [],
            'without_tags' => []
        ];
    }

    $data_json = json_encode($params['data'], JSON_UNESCAPED_SLASHES);

    // Armar URL manualmente, sin http_build_query
    $url = $config['property_search_url']
        . '?lang=es_ar'
        . '&format=json'
        . '&limit=' . $limit
        . '&offset=' . $offset
        . '&order_by=' . urlencode($order_by)
        . '&order=' . urlencode($order)
        . '&data=' . urlencode($data_json)
        . '&key=' . $config['api_token'];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return [];
    }

    curl_close($ch);

    $decoded = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON decode error: ' . json_last_error_msg();
        return [];
    }

    return $decoded ?: [];
}

function get_only_property_by_filter(array $params = []): array{

    $data = get_summary_locations($params);

    return $data['objects'] ?? [];

}

function get_search_summary(array $params = []): array{
    $config = require get_template_directory().'/tokko-api/config.php';



    if (!isset($params['data'])) {
        $params['data'] = [
            'current_localization_id' => 1,
            'current_localization_type' => 'country',
            'price_from' => 1,
            'price_to' => 999999999,
            'operation_types' => [1,2,3],
            'property_types' => range(1, 28),
            'currency' => 'ANY',
            'filters' => []
        ];

    }
   
    
    $params['data'] = json_encode($params['data']);

    $params['key'] = $config['api_token'];
    $params['format'] = 'json';
    $params['lang'] = 'es_ar';
    $url = $config['get_search_summary_url'] .'?' .http_build_query($params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Curl error: '.curl_error($ch);
        curl_close($ch);
        return [];
    }
    curl_close($ch);
    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON decode error: '.json_last_error_msg();
        return [];
    }
    return $decoded? : [];
}



// --- FUNCIÓN PRINCIPAL ---
function get_create_filter_data(array $dataFilter = []): array {

    // DATA FILTER
    $data_locations = $dataFilter['locations'] ?? [];
    $data_type_operation = $dataFilter['operation_types'] ?? [];
    $data_type_property = $dataFilter['property_types'] ?? [];
    $data_age = $dataFilter['age'] ?? [];
    $data_room = $dataFilter['room_amount'] ?? [];
    $suite_amount = $dataFilter['suite_amount'] ?? [];

    // QUERYS PARAMS
  
    $type_operation = isset($_GET['operacion']) ? sanitize_text_field($_GET['operacion']) : [1,2];
    $type_operation = is_array($type_operation) ? $type_operation : [$type_operation];


    // QUERY LOCATION
    $location = $_GET['localidad'] ?? [];
    $location = is_array($location) ? $location : [$location];
    $location = array_map('intval', $location);


    // QUERY TIPO PROPIEDAD
    $type_property = $_GET['tipo'] ?? [];
    $type_property = is_array($type_property) ? $type_property : [$type_property];
    $type_property = array_map('intval', $type_property);

    // QUERY ANTIGUEDAD
    $age = isset($_GET['antiguedad']) ? $_GET['antiguedad'] : [];


    // Query Ambientes
    $ambientes_slug = isset($_GET['ambientes']) ? (int) $_GET['ambientes'] : 0;
    $dormitorios_slug = isset($_GET['dormitorio']) ? (int) $_GET['dormitorio'] : 0;


    
    return [
        'Tipo de operación' => generar_filtros_operacion($data_type_operation, $type_operation),
        'Ubicación' => generar_filtros_ubicacion($location),
        'Tipo de propiedad' => generar_filtros_tipo($data_type_property, $type_property),
        'Antiguedad' => generar_filtros_antiguedad($data_age, $age),
        'Ambientes' => generar_filtros_ambientes($data_room, $ambientes_slug),
        'Dormitorios' => generar_filtros_dormitorios($suite_amount, $dormitorios_slug),
    ];
}
// FILTROS SERVICIOS Y OTROS
function get_create_filter_tag(array $dataFilter = []): array {



    $servicios = $_GET['servicios'] ?? [];
    $servicios = is_array($servicios) ? $servicios : [$servicios];
    $servicios = array_map('intval', $servicios);

    $otros = $_GET['otros'] ?? [];
    $otros = is_array($otros) ? $otros : [$otros];
    $otros = array_map('intval', $otros);


    $all_tag = get_only_services($dataFilter['tags'] ?? []);
    $all_other_tag = get_only_other_tags($dataFilter['tags'] ?? []);

    return [
        'Servicios' => generar_filtros_servicios($all_tag, $servicios),
        'Otros' => generar_filtros_otros_servicios($all_other_tag, $otros)
    ];
}

// FUNCION PARA CAPTURAR DATO DE UNA PROPIEDAD
function get_details_property(int $id): array {
    $config = require get_template_directory() . '/tokko-api/config.php';
    $params = [
        'key' => $config['api_token'],
        'format' => 'json',
        'lang' => 'es_ar',
        'id' => $id
    ];

    $url = $config['property_detail_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch);
        curl_close($ch);
        return [];
    }

    curl_close($ch);

    $decoded = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'JSON decode error: ' . json_last_error_msg();
        return [];
    }

    return $decoded ?: [];
}



