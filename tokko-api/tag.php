<?php 

function get_all_tag_property(array $params = [], int $limit = 1000, int $offset = 0, $array = 0): array {
    $config = require get_template_directory().'/tokko-api/config.php';
    $params['key'] = $config['api_token'];
    $params['format'] = 'json';
    $params['lang'] = 'es_ar';
    $params['limit'] = $limit;
    $params['offset'] = $offset;

    // Check if 'data' key exists before attempting to encode it
    if (isset($params['data'])) {
        $params['data'] = json_encode($params['data']);
    } else {
        // If 'data' is not set, you might want to initialize it to null, an empty string, or an empty JSON object,
        // depending on what the API expects when no 'data' is provided.
        // For now, I'll set it to an empty JSON object string if it's missing,
        // assuming the API expects it even if empty.
        $params['data'] = json_encode(new stdClass()); // Represents an empty JSON object {}
    }

    $url = $config['property_tag_url']
        .'?'
        .http_build_query($params);

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
    return $decoded ?: [];
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