<?php 
function get_developments(array $params = []): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    $data_json = json_encode($params);
    $cache_key = 'tokko_developments_' . md5($data_json);

    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    if (is_bot()) {
        error_log('[TOKKO][BOT] Cache no encontrada para developmets');
        return [];
    }

    $params['key'] = $config['api_token'];
    $params = array_merge([
        'format' => 'json',
        'lang' => 'es_ar'
    ], $params);

    $url = $config['developments_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $data = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            set_transient($cache_key, $data, HOUR_IN_SECONDS);
            return $data;
        } else {
            error_log("[TOKKO][DEVELOPMENTS] JSON inválido: " . json_last_error_msg());
        }
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
    $log_file = __DIR__ . '/tokko-debug.log';
    $config = require get_template_directory() . '/tokko-api/config.php';

    $cache_key = 'tokko_development_' . $id;
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    if (is_bot()) {
        error_log("[TOKKO][BOT] Cache no encontrada para desarrollo ID {$id}");
        return [];
    }

    if (empty($config['api_token']) || empty($config['developments_url'])) {
        file_put_contents($log_file, "[TOKKO][{$id}] Sin config\n", FILE_APPEND);
        return [];
    }

    $url = rtrim($config['developments_url'], '/') . '/' . $id . '/?' . http_build_query([
        'key'    => $config['api_token'],
        'format' => 'json',
        'lang'   => 'es_ar',
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents($log_file, "[TOKKO][{$id}] HTTP: $http_code - Error: $error\n", FILE_APPEND);

    if ($http_code === 200 && !$error) {
        $data = json_decode($response, true);
        if (is_array($data) && !empty($data)) {
            file_put_contents($log_file, "[TOKKO][{$id}] OK\n", FILE_APPEND);
            $result = ['objects' => [$data]];
            set_transient($cache_key, $result, HOUR_IN_SECONDS);
            return $result;
        } else {
            file_put_contents($log_file, "[TOKKO][{$id}] JSON vacío\n", FILE_APPEND);
        }
    }

    return [];
}

function get_development_units(int $id): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    $cache_key = 'tokko_development_units_' . $id;
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    if (is_bot()) {
        error_log("[TOKKO][BOT] Cache no encontrada para unidades desarrollo ID {$id}");
        return [];
    }

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

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
        set_transient($cache_key, $data, HOUR_IN_SECONDS);
        return $data;
    }

    return [];
}


