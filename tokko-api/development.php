<?php 
function get_developments(array $params = []): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    $params['key'] = $config['api_token'];
    $params = array_merge([
        'format' => 'json',
        'lang' => 'es_ar',
    ], $params);

    $url = $config['developments_url'] . '?' . http_build_query($params);
    contar_llamada_api($config['developments_url']);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }
        error_log("[TOKKO][DEVELOPMENTS] JSON inválido: " . json_last_error_msg());
    } else {
        error_log("[TOKKO][DEVELOPMENTS] CURL error: $error | HTTP: $http_code");
    }
    
    return [];
}

function filter_developments(array $params = [], array $filters = [], int $limit = 0): array {
    $data = get_developments($params);

    if (!isset($data['objects']) || !is_array($data['objects'])) {
        return [];
    }

    $filtered = array_filter($data['objects'], function ($item) use ($filters) {
        foreach ($filters as $key => $value) {
            if (!isset($item[$key]) || $item[$key] != $value) {
                return false;
            }
        }
        return true;
    });

    if ($limit > 0) {
        $filtered = array_slice($filtered, 0, $limit);
    }

    return array_values($filtered);
}

function get_development_by_id(int $id): array {
 
    $config = require get_template_directory() . '/tokko-api/config.php';

    if (empty($config['api_token']) || empty($config['developments_url'])) {
       
        return [];
    }

    // 🔹 Cache: clave única por ID
    $cache_key = 'tokko_dev_' . $id;
    $cache_time = 300; // segundos
    $cached = get_transient($cache_key);
    if ($cached !== false) {

        return $cached;
    }

    $url = rtrim($config['developments_url'], '/') . '/' . $id . '/?' . http_build_query([
        'key'    => $config['api_token'],
        'format' => 'json',
        'lang'   => 'es_ar',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);



    if ($http_code === 200 && !$error) {
        $data = json_decode($response, true);
        if (is_array($data) && !empty($data)) {
           

            // 🔹 Guardar en cache
            $result = ['objects' => [$data]];
            set_transient($cache_key, $result, $cache_time);
            return $result;
        } else {
           
        }
    }

    return [];
}


function get_development_units(int $id): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    $params = [
        'key' => $config['api_token'],
        'format' => 'json',
        'lang' => 'es_ar',
        'data' => json_encode([
            "current_localization_id" => 1,
            "current_localization_type" => "country",
            "price_from" => 0,
            "price_to" => 999999999999,
            "operation_types" => [1, 2, 3],
            "property_types" => [1, 2, 3, 4, 5, 6, 7],
            "currency" => "USD",
            "filters" => [
                ["development__id", "=", (string)$id]
            ]
        ])
    ];

    $url = $config['development_units_url'] . '?' . http_build_query($params);
    contar_llamada_api($config['development_units_url']);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        return $data;
    }
    error_log("[TOKKO][DEVELOPMENT_UNITS] CURL error: $error");
    
    return [];
}


function get_detail_development_by_json(int $id) {
    $file_path = get_template_directory() . '/tokko-api/data/developments.json';
    if (!file_exists($file_path)) return [];

    $json = file_get_contents($file_path);
    $data = json_decode($json, true);
    if (empty($data) || !is_array($data)) return [];

    if (!isset($data['objects']) || !is_array($data['objects'])) return [];

    foreach ($data['objects'] as $development) {
        if (isset($development['id']) && $development['id'] === $id) {
            return $development;
        }
    }

    return []; // no encontrado
}