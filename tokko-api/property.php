<?php 





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

    // Si es bot: evitamos llamada
    if (is_bot()) {
        error_log('[TOKKO][BOT] Llamada bloqueada para bot');
        return [];
    }

    $data_json = json_encode($params['data'], JSON_UNESCAPED_SLASHES);

    // Fetch desde API sin cache
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
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        } else {
            error_log("[TOKKO] JSON inválido: " . json_last_error_msg());
        }
    } else {
        error_log("[TOKKO] CURL Error: $error | HTTP: $http_code");
    }

    return [];
}



function get_only_property_by_filter(array $params = []): array {
    $cache_key = 'tokko_summary_' . md5(json_encode($params));

    // Intentar cache
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    // Si es bot y no hay cache, evitar llamada
    if (is_bot()) {
        error_log('[TOKKO][BOT] Cache no encontrada para ' . $cache_key);
        return [];
    }

    $data = get_summary_locations($params);
    $result = $data['objects'] ?? [];

    set_transient($cache_key, $result, HOUR_IN_SECONDS);
    return $result;
}



function get_search_summary(array $params = []): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    if (!isset($params['data'])) {
        $params['data'] = [
            'current_localization_id' => 1,
            'current_localization_type' => 'country',
            'price_from' => 1,
            'price_to' => 999999999,
            'operation_types' => [1, 2, 3],
            'property_types' => range(1, 28),
            'currency' => 'ANY',
            'filters' => []
        ];
    }

    $data_json = json_encode($params['data'], JSON_UNESCAPED_SLASHES);
    $params['data'] = $data_json;
    $params['key'] = $config['api_token'];
    $params['format'] = 'json';
    $params['lang'] = 'es_ar';

    $cache_key = 'tokko_search_summary_' . md5($data_json);

    // Intentar cache
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    // Evitar llamada si es bot y no hay cache
    if (is_bot()) {
        error_log('[TOKKO][BOT] Cache no encontrada para ' . $cache_key);
        return [];
    }

    $url = $config['get_search_summary_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            set_transient($cache_key, $decoded, HOUR_IN_SECONDS);
            return $decoded;
        } else {
            error_log("[TOKKO][SUMMARY] JSON inválido: " . json_last_error_msg());
        }
    } else {
        error_log("[TOKKO][SUMMARY] CURL Error: $error | HTTP: $http_code");
    }

    return [];
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
    $age = (array) ($_GET['antiguedad'] ?? []);

    // Query Ambientes (multi selección)
    $ambientes_slug = $_GET['ambientes'] ?? [];
    $ambientes_slug = is_array($ambientes_slug) ? $ambientes_slug : explode(',', $ambientes_slug);
    $ambientes_slug = array_map('sanitize_text_field', $ambientes_slug);
    $ambientes_slug = array_unique(array_filter($ambientes_slug)); // limpio

    // Query Dormitorios (multi selección, misma lógica que ambientes)
    $dormitorios_slug = $_GET['dormitorio'] ?? [];
    $dormitorios_slug = is_array($dormitorios_slug) ? $dormitorios_slug : explode(',', $dormitorios_slug);
    $dormitorios_slug = array_map('sanitize_text_field', $dormitorios_slug);
    $dormitorios_slug = array_unique(array_filter($dormitorios_slug)); // limpio

    return [
        'Tipo de operación' => generar_filtros_operacion($data_type_operation, $type_operation),
        'Ubicación' => generar_filtros_ubicacion($location),
        'Tipo de propiedad' => generar_filtros_tipo($data_type_property, $type_property),
        'Antiguedad' => generar_filtros_antiguedad($age),
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

    $cache_key = 'tokko_detail_' . $id;

    // Intentar cache
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    // Evitar llamada si es bot y no hay cache
    if (is_bot()) {
        error_log('[TOKKO][BOT] Cache no encontrada para detalle ' . $id);
        return [];
    }

    $params = [
        'key'    => $config['api_token'],
        'format' => 'json',
        'lang'   => 'es_ar',
        'id'     => $id
    ];

    $url = $config['property_detail_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            set_transient($cache_key, $decoded, HOUR_IN_SECONDS);
            return $decoded;
        } else {
            error_log("[TOKKO][DETAIL {$id}] JSON inválido: " . json_last_error_msg());
        }
    } else {
        error_log("[TOKKO][DETAIL {$id}] CURL Error: $error | HTTP: $http_code");
    }

    return [];
}





