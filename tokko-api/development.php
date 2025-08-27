<?php 
function get_developments(array $params = []): array {

    $config = require get_template_directory() . '/tokko-api/config.php';
    $log_file = get_template_directory() . '/tokko.log';
    $ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';

    $params['key'] = $config['api_token'];
    $params = array_merge([
        'format' => 'json',
        'lang' => 'es_ar',
    ], $params);

    $transient_key = md5(json_encode($params));
    $transient_new = 'tokko_developments_new_' . $transient_key;
    $transient_old = 'tokko_developments_old_' . $transient_key;
    $new_time      = 120;   // 2 minutos
    $old_time      = 3600;  // 1 hora

    // 1. Bots: usar NEW si existe, sino OLD
    if (is_bot()) {
        $new_cache = get_transient($transient_new);
        $old_cache = get_transient($transient_old);

        if ($new_cache !== false) {
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] ALL DEVELOPMENT Bot recibe NEW transient (IP: {$ip_cliente})\n", FILE_APPEND);
            return $new_cache;
        } elseif ($old_cache !== false) {
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] ALL DEVELOPMENT Bot recibe OLD transient (IP: {$ip_cliente})\n", FILE_APPEND);
            return $old_cache;
        } else {
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] ALL DEVELOPMENT Bot sin transient, placeholder entregado (IP: {$ip_cliente})\n", FILE_APPEND);
            return ['developments'=>[], 'placeholder'=>true];
        }
    }

    // 2. Usuario normal: verificar NEW transient primero
    $cached_transient = get_transient($transient_new);
    if ($cached_transient !== false) {
        file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] ALL DEVELOPMENT Transient NEW entregado a usuario (IP: {$ip_cliente})\n", FILE_APPEND);
        return $cached_transient;
    }

    // 3. Fetch API
    $url = $config['developments_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | ALL DEVELOPMENT | HTTP: {$http_code} | URL: {$url} | Error: {$error}\n", FILE_APPEND);

    if ($response && $http_code === 200) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {

            // Guardar NEW transient
            set_transient($transient_new, $data, $new_time);

            // Actualizar OLD transient para bots
            set_transient($transient_old, $data, $old_time);

            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] ALL DEVELOPMENT Transient NEW creado para usuario y OLD actualizado para bots (IP: {$ip_cliente})\n", FILE_APPEND);

            return $data;
        }

        file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] ALL DEVELOPMENT JSON inválido: ".json_last_error_msg()."\n", FILE_APPEND);
    } else {
        file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] ALL DEVELOPMENT  CURL error: {$error} | HTTP: {$http_code}\n", FILE_APPEND);
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
    $log_file = get_template_directory() . '/tokko.log';
    $ip_cliente = $_SERVER['REMOTE_ADDR'] ?? '';

    if (empty($config['api_token']) || empty($config['developments_url'])) {
        return [];
    }

    $transient_new = 'tokko_dev_new_' . $id;
    $transient_old = 'tokko_dev_old_' . $id;
    $new_time      = 120;   // 2 minutos
    $old_time      = 3600;  // 1 hora

    // 1. Bots: usar NEW si existe, sino OLD
    if (is_bot()) {
        $new_cache = get_transient($transient_new);
        $old_cache = get_transient($transient_old);

        if ($new_cache !== false) {
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] DEVELOPMENT_ID Bot recibe NEW transient (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
            return $new_cache;
        } elseif ($old_cache !== false) {
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] DEVELOPMENT_ID Bot recibe OLD transient (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
            return $old_cache;
        } else {
            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] DEVELOPMENT_ID Bot sin transient, placeholder entregado (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
            return ['objects'=>[], 'placeholder'=>true];
        }
    }

    // 2. Usuario normal: verificar transient NEW primero
    $cached_transient = get_transient($transient_new);
    if ($cached_transient !== false) {
        file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] DEVELOPMENT_ID Transient NEW entregado a usuario (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);
        return $cached_transient;
    }

    // 3. Fetch API
    $url = rtrim($config['developments_url'], '/') . '/' . $id . '/?' . http_build_query([
        'key'    => $config['api_token'],
        'format' => 'json',
        'lang'   => 'es_ar',
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_FOLLOWLOCATION => true,
    ]);

    $response  = curl_exec($ch);
    $error     = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] IP: {$ip_cliente} | DEVELOPMENT_ID | HTTP: {$http_code} | URL: {$url} | Error: {$error}\n", FILE_APPEND);

    if ($http_code === 200 && !$error) {
        $data = json_decode($response, true);
        if (is_array($data) && !empty($data)) {
            $result = ['objects' => [$data]];

            // Guardar NEW transient
            set_transient($transient_new, $result, $new_time);

            // Actualizar OLD transient para bots
            set_transient($transient_old, $result, $old_time);

            file_put_contents($log_file, "[".date('Y-m-d H:i:s')."] DEVELOPMENT_ID Transient NEW creado para usuario y OLD actualizado para bots (ID: {$id} | IP: {$ip_cliente})\n", FILE_APPEND);

            return $result;
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
            "current_localization_id" => 0,
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