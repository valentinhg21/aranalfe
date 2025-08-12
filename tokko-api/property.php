<?php 

function get_all_property_by_filter(
    array $params = [],
    int $limit = 12,
    int $offset = 0,
    string $order_by = "created_date",
    string $order = 'ASC'
): array {
    $config = require get_template_directory() . '/tokko-api/config.php';
    $log_file = get_template_directory() . '/tokko.log';

    // Obtener IP del cliente
    $ip_cliente = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'CLI';

    // Bloquear IPs sospechosas y bots
    // if (is_bot() || in_array($ip_cliente)) {
    //     file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Acceso bloqueado para IP: {$ip_cliente}, User-Agent: {$_SERVER['HTTP_USER_AGENT']}\n", FILE_APPEND);
    //     return [];
    // }

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

    $cache_key = 'tokko_props_' . md5(json_encode([$params, $limit, $offset, $order_by, $order]));
    $cache_time = 300;

    $cached = get_transient($cache_key);
    if ($cached !== false) {
        file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | get_all_property_by_filter | TOKKO cache hit\n", FILE_APPEND);
        return $cached;
    }

    $data_json = json_encode($params['data'], JSON_UNESCAPED_SLASHES);
    $url = $config['property_search_url']
        . '?lang=es_ar'
        . '&format=json'
        . '&limit=' . $limit
        . '&offset=' . $offset
        . '&order_by=' . urlencode($order_by)
        . '&order=' . urlencode($order)
        . '&data=' . urlencode($data_json)
        . '&key=' . $config['api_token'];

    $max_intentos = 3;
    $intento = 0;
    contar_llamada_api($config['property_search_url']);

    do {
        file_put_contents(
            $log_file,
            "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | Intento ".($intento+1)." | get_all_property_by_filter | URL: {$url}\n",
            FILE_APPEND
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        file_put_contents(
            $log_file,
            "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | Intento ".($intento+1)." | get_all_property_by_filter | HTTP: {$http_code} | Error: {$error}\n",
            FILE_APPEND
        );

        if ($response && $http_code === 200) {
            $decoded = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                set_transient($cache_key, $decoded, $cache_time);
                return $decoded;
            } else {
                file_put_contents(
                    $log_file,
                    "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | get_all_property_by_filter | JSON inválido: ".json_last_error_msg()."\n",
                    FILE_APPEND
                );
            }
        }

        $intento++;
        sleep(1);
    } while ($intento < $max_intentos);

    return [];
}

function get_all_property_by_filter_test(array $params = [], int $limit = 12, int $offset = 0, string $order_by = "created_date", string $order = 'ASC'): array {
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
    $url = $config['property_search_url']
        . '?lang=es_ar'
        . '&format=json'
        . '&limit=' . $limit
        . '&offset=' . $offset
        . '&order_by=' . urlencode($order_by)
        . '&order=' . urlencode($order)
        . '&data=' . urlencode($data_json)
        . '&key=' . $config['api_token'];

    // Debug local
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo "<pre>URL: $url</pre>";
    }

    $max_intentos = 3;
    $intento = 0;
    $response = null;
    $http_code = 0;

    do {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20); // tiempo razonable
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
                error_log("[TOKKO] RAW response: " . substr($response, 0, 500)); // acotado por si es largo
            }
        } else {
            error_log("[TOKKO] Intento $intento: CURL Error: $error | HTTP: $http_code");
        }

        $intento++;
        sleep(1); // espera entre reintentos
    } while ($intento < $max_intentos);

    return [];
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

    $url = $config['get_search_summary_url'] . '?' . http_build_query($params);
    contar_llamada_api($config['get_search_summary_url']);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        error_log("[TOKKO][SUMMARY] JSON inválido: " . json_last_error_msg());
    } else {
        error_log("[TOKKO][SUMMARY] CURL Error: $error | HTTP: $http_code");
    }
    
    return [];
}


function get_only_property_by_filter(array $params = []): array {
    $data = get_summary_locations($params);
    return $data['objects'] ?? [];
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
        'Tipo de operación' => generar_filtros_operacion($type_operation),
        'Ubicación' => generar_filtros_ubicacion($location),
        'Tipo de propiedad' => generar_filtros_tipo($type_property),
        'Antiguedad' => generar_filtros_antiguedad($age),
        'Ambientes' => generar_filtros_ambientes($ambientes_slug),
        'Dormitorios' => generar_filtros_dormitorios($dormitorios_slug),
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
        'key'    => $config['api_token'],
        'format' => 'json',
        'lang'   => 'es_ar',
        'id'     => $id
    ];

    $url = $config['property_detail_url'] . '?' . http_build_query($params);
    contar_llamada_api($config['property_detail_url']);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        error_log("[TOKKO][DETAIL {$id}] JSON inválido: " . json_last_error_msg());
    } else {
        error_log("[TOKKO][DETAIL {$id}] CURL Error: $error | HTTP: $http_code");
    }
    
    return [];
}





