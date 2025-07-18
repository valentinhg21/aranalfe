<?php 



function get_all_tag_property(array $params = [], int $limit = 1000, int $offset = 0, $array = 0): array {
    $config = require get_template_directory() . '/tokko-api/config.php';

    $params['key'] = $config['api_token'];
    $params['format'] = 'json';
    $params['lang'] = 'es_ar';
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    $params['data'] = isset($params['data'])
        ? json_encode($params['data'])
        : json_encode(new stdClass()); // {}

    $cache_key = 'tokko_tag_property_' . md5(http_build_query($params));
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    if (is_bot()) {
        error_log('[TOKKO][BOT] Cache no encontrada para tags');
        return [];
    }

    $url = $config['property_tag_url'] . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $decoded = json_decode($response, true);

        if (is_array($decoded) && !empty($decoded)) {
            set_transient($cache_key, $decoded, HOUR_IN_SECONDS);
            return $decoded;
        } else {
            error_log("[TOKKO][TAG] JSON inválido o vacío");
        }
    } else {
        error_log("[TOKKO][TAG] CURL error: $error | HTTP: $http_code");
    }

    return [];
}


function get_only_services($data) {
    $data = $data ?? [];
    return array_filter($data, function($item) {
        return isset($item['tag_type']) && $item['tag_type'] === 1;
    });
}

function get_only_other_tags($data){
    $data = $data ?? [];
    return array_filter($data, function($item) {
        return isset($item['tag_type']) && $item['tag_type'] === 2;
    });
}