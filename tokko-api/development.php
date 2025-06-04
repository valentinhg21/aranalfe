<?php 
function get_developments(array $params = []): array{
    $config = require get_template_directory() . '/tokko-api/config.php';
    // Agregar la clave a los parámetros
    $params['key'] = $config['api_token'];
    // Agregar valores por defecto si no existen
    $params = array_merge([
        'format' => 'json',
        'lang' => 'es_ar'
    ], $params);
    $url = $config['developments_url'] . '?' . http_build_query($params);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
   return json_decode($response, true) ?: [];
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
