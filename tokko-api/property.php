<?php 
// $_SERVER['REMOTE_ADDR'] = '66.249.70.64';
// $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
function get_all_property_by_filter(
    array $params = [],
    int $limit = 12,
    int $offset = 0,
    string $order_by = "created_date",
    string $order = 'ASC'
): array {

    $config     = require get_template_directory() . '/tokko-api/config.php';
    $log_file   = get_template_directory() . '/tokko.log';
    $ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';

    if (!isset($params['data'])) {
        $params['data'] = [
            'current_localization_id' => 0,
            'current_localization_type' => 'country',
            'price_from'   => 1,
            'price_to'     => 999999999,
            'operation_types' => [1, 2],
            'property_types'  => range(1, 28),
            'currency'    => 'ANY',
            'filters'     => [],
            'with_tags'   => [],
            'without_tags'=> []
        ];
    }

    $transient_new = 'tokko_props_new_' . md5(json_encode([$params, $limit, $offset, $order_by, $order]));
    $transient_old = 'tokko_props_old';
    $new_time      = 120;   // 2 minutos
    $old_time      = 86400;  // 1 hora

    // 1. Bots: usar NEW si existe, sino OLD
    if (is_bot()) {
        $new_cache = get_transient($transient_new);
        $old_cache = get_transient($transient_old);
        if(TOKKO_LOG){
            if ($new_cache !== false) {
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Bot recibe NEW transient (IP: {$ip_cliente})\n", FILE_APPEND);
                return $new_cache;
            } elseif ($old_cache !== false) {
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Bot recibe OLD transient (IP: {$ip_cliente})\n", FILE_APPEND);
                return $old_cache;
            } else {
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Bot sin transient, placeholder entregado (IP: {$ip_cliente})\n", FILE_APPEND);
                return ['properties'=>[], 'placeholder'=>true];
            }
        }
    }

    // 2. Usuario normal: verificar transient NEW primero
    $cached_transient = get_transient($transient_new);
    if ($cached_transient !== false) {
        if(TOKKO_LOG){
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Transient NEW entregado a usuario (IP: {$ip_cliente})\n", FILE_APPEND);
           
        }
         return $cached_transient;
    }

    // 3. Fetch API
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

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_FOLLOWLOCATION => true
    ]);

    $response  = curl_exec($ch);
    $error     = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if(TOKKO_LOG){
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | HTTP: {$http_code} | URL: {$url} | Error: {$error}\n", FILE_APPEND);
    }


    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {

            // Guardar NEW transient (usuarios)
            set_transient($transient_new, $decoded, $new_time);

            // Actualizar OLD transient (bots) solo si no existe o siempre reemplazar
            set_transient($transient_old, $decoded, $old_time);

            if(TOKKO_LOG){

                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Transient NEW creado para usuario y OLD actualizado para bots (IP: {$ip_cliente})\n", FILE_APPEND);
         
            }


            return $decoded;
        }

        if(TOKKO_LOG){
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] JSON inválido: ".json_last_error_msg()."\n", FILE_APPEND);
        }
     
        

        
    }

    return [];
}

function get_search_summary(array $params = []): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    if (!isset($params['data'])) {
        $params['data'] = [
            'current_localization_id' => 0,
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
    $type_operation = $_GET['operacion'] ?? []; // si no viene, default [1,2]
    $type_operation = is_array($type_operation) ? $type_operation : [$type_operation];
    $type_operation = array_map('intval', $type_operation); // asegurarse de que sean enteros

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
    $log_file = get_template_directory() . '/tokko.log';
    $ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';

    $params = [
        'key'    => $config['api_token'],
        'format' => 'json',
        'lang'   => 'es_ar',
        'id'     => $id
    ];

    $transient_key = md5(json_encode($params));
    $transient_new = 'tokko_detail_new_' . $transient_key;
    $transient_old = 'tokko_detail_old_' . $transient_key;
    $new_time      = 120;   // 2 minutos
    $old_time      = 3600;  // 1 hora

    // 1. Bots: usar NEW si existe, sino OLD
    if (is_bot()) {
        $new_cache = get_transient($transient_new);
        $old_cache = get_transient($transient_old);
        if(TOKKO_LOG){
            if ($new_cache !== false) {
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Single Property Bot recibe NEW transient (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
                return $new_cache;
            } elseif ($old_cache !== false) {
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Single Property Bot recibe OLD transient (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
                return $old_cache;
            } else {
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Single Property Bot sin transient, placeholder entregado (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
                return ['property'=>[], 'placeholder'=>true];
            }
        }
    }

    // 2. Usuario normal: verificar NEW transient primero
    $cached_transient = get_transient($transient_new);
  
        if ($cached_transient !== false) {
           if(TOKKO_LOG){
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Single Property Transient NEW entregado a usuario (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
            }
            return $cached_transient;
        }
   


    // 3. Fetch API
    $url = $config['property_detail_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if(TOKKO_LOG){
        file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | Single Property | HTTP: {$http_code} | URL: {$url} | Error: {$error}\n", FILE_APPEND);
    }


    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {

            // Guardar NEW transient
            set_transient($transient_new, $decoded, $new_time);

            // Actualizar OLD transient para bots
            set_transient($transient_old, $decoded, $old_time);
            if(TOKKO_LOG){
                file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Single Property Transient NEW creado para usuario y OLD actualizado para bots (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
            }


            return $decoded;
        }
        if(TOKKO_LOG){
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Single Property JSON inválido: ".json_last_error_msg()."\n", FILE_APPEND);
        }

    } else {
        file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] Single Property CURL Error: {$error} | HTTP: {$http_code}\n", FILE_APPEND);
    }

    return [];
}