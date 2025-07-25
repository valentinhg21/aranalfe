<?php

function get_summary_locations(array $params = []): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    if (!isset($params['data'])) {
        $params['data'] = [
            'current_localization_id' => 0,
            'current_localization_type' => 'country',
            'price_from' => 1,
            'price_to' => 999999999,
            'operation_types' => [1, 2],
            'property_types' => range(1, 25),
            'currency' => 'ANY',
            'filters' => []
        ];
    }

    $data_json = is_array($params['data']) ? json_encode($params['data']) : $params['data'];
    $cache_key = 'tokko_summary_locations_' . md5($data_json);

    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    if (is_bot()) {
        error_log('[TOKKO][BOT] Cache no encontrada para locations');
        return [];
    }

    $params['data'] = $data_json;
    $params['key'] = $config['api_token'];
    $params = array_merge([
        'format' => 'json',
        'lang' => 'es_ar'
    ], $params);

    $url = $config['locations_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
            error_log("[TOKKO][LOCATIONS] JSON inválido: " . json_last_error_msg());
        }
    } else {
        error_log("[TOKKO][LOCATIONS] CURL error: $error | HTTP: $http_code");
    }

    return [];
}


function get_only_locations(array $params = []): array {
    $data = get_summary_locations($params);
    return $data['objects']['locations'] ?? [];
}

function save_data_locations(array $params = []) {
    $data = get_only_locations($params);
    if (!is_array($data)) return;
    $file_path = get_template_directory() . '/tokko-api/data/locations.json';
    file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}



function get_only_parent_locations(array $params = []): array {
    $data = get_summary_locations($params);
    $locations = $data['objects']['locations'] ?? [];
    $unique = [];
    foreach ($locations as $location) {
        $parent_name = $location['parent_name'] ?? null;
        $parent_id = $location['parent_id'] ?? null;
        $count = $location['count'] ?? 0;

        if ($parent_name && $parent_id && $count >= 1) {
            $key = $parent_id . '|' . $parent_name;
            $unique[$key] = [
                'parent_name' => $parent_name,
                'parent_id' => $parent_id
            ];
        }
    }
    return array_values($unique);
}


function get_only_property_types(array $params = []): array {
    $data = get_summary_locations($params);
    $types = $data['objects']['property_types'] ?? [];

    return array_map(function ($item) {
        if ($item['type'] === 'Local') {
            $item['type'] = 'Locales';
        }
        return $item;
    }, $types);
}

function split_slug_by_valid_terms(string $slug, array $valid_slugs): array {
    $result = [];
    $slug_parts = explode('-', $slug);

    $i = 0;
    while ($i < count($slug_parts)) {
        $match_found = false;
        for ($j = count($slug_parts); $j > $i; $j--) {
            $segment = implode('-', array_slice($slug_parts, $i, $j - $i));
            if (in_array($segment, $valid_slugs)) {
                $result[] = $segment;
                $i = $j;
                $match_found = true;
                break;
            }
        }
        if (!$match_found) {
            $i++; // evita loop infinito si no hay match
        }
    }

    return $result;
}

function get_tokko_ids_from_slugs(array $tipo_slug = [], array $ubicacion_slug = [], array $ubicacion_localidad = []): array {
    $result = [
        'location_id' => [],
        'property_type_id' => [],
        'location_localidad_id' => []
    ];


    // 2. Buscar todos los property_type_id que coincidan (ej: "departamento")
    $property_types = get_only_property_types();
    foreach ($property_types as $type) {
        $slug = sanitize_title($type['type']);
        if (in_array($slug, $tipo_slug)) {
            $result['property_type_id'][] = (int)$type['id'];
        }
    }

    // 1. Buscar todos los location_id que coincidan con el parent_name (ej: "Capital Federal")
    $locations = get_only_parent_locations();
    foreach ($locations as $loc) {
        $slug = sanitize_title($loc['parent_name']);

        if (in_array($slug, $ubicacion_slug)) {
            $result['location_id'][] = (int)$loc['parent_id'];
        }
    }

    // 3. Buscar todas las localidades que coincidan (ej: "caballito")
    if (!empty($ubicacion_localidad)) {
        $property_locations = get_only_locations();
        foreach ($property_locations as $localidad) {
            $slug = sanitize_title($localidad['location_name']);
            if (in_array($slug, $ubicacion_localidad)) {
                $result['location_localidad_id'][] = (int)$localidad['location_id'];
            }
        }
    }

    return $result;
}

// Hooks
add_action('wp_ajax_get_data_search', 'get_data_search');
add_action('wp_ajax_nopriv_get_data_search', 'get_data_search');
function get_data_search() {
    $body = json_decode(file_get_contents('php://input'), true);
    $post_data = is_array($body) ? $body : [];

    // Definir valores por defecto
    $default_data = [
        'current_localization_id' => 0,
        'current_localization_type' => 'country',
        'price_from' => 1,
        'price_to' => 999999999,
        'operation_types' => [1, 2, 3, 4, 5, 6, 7, 8],
        'property_types' => [1, 2, 3, 4, 5, 6, 7, 8],
        'currency' => 'ANY',
        'filters' => []
    ];

    $merged_data = array_replace($default_data, $post_data);
    $params = ['data' => $merged_data];

    wp_send_json_success([
        'property_types'    => get_only_property_types($params),
        'parent_locations'  => get_only_parent_locations($params),
        'locations'         => get_only_locations($params),
        'params'            => $params
    ]);
}
