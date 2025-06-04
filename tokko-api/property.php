<?php 
function get_all_property_by_filter(array $params = [], int $limit = 12, int $offset = 0, $array = 0): array {

    $config = require get_template_directory().'/tokko-api/config.php';
    if (!isset($params['data'])) {
        $params['data'] = [
            'current_localization_id' => 1,
            'current_localization_type' => 'country',
            'price_from' => 1,
            'price_to' => 999999999,
            'operation_types' => [1],
            'property_types' => [
                1,
                2,
                3,
                4,
                5,
                6,
                7,
                8
            ],
            'currency' => 'ANY',
            'filters' => []
        ];

    }

    $params['key'] = $config['api_token'];

    $params['format'] = 'json';

    $params['lang'] = 'es_ar';

    $params['limit'] = $limit;

    $params['offset'] = $offset;

    $params['data'] = json_encode($params['data']);

    $url = $config['property_search_url']
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

    return $decoded? : [];

}

function get_only_property_by_filter(array $params = []): array{

    $data = get_summary_locations($params);

    return $data['objects'] ?? [];

}

// --- FUNCIÓN PRINCIPAL ---
function get_create_filter_data(array $data = [], array $dataFiltrada = []): array {
    $operacion_slug = get_query_var('operacion');
    $tipo_slug = get_query_var('tipo');
    $ubicacion_slug = get_query_var('ubicacion');
    $localidad_slug = get_query_var('localidad');
    $antiguedad_slug = get_query_var('antiguedad');
    $ambientes_slug = isset($_GET['ambientes']) ? sanitize_text_field($_GET['ambientes']) : '';
    $dormitorios_slug = isset($_GET['dormitorios']) ? sanitize_text_field($_GET['dormitorios']) : '';

    $all_localidades = get_only_locations();
    $localidades_validas = sanitize_slugs(array_column($all_localidades, 'location_name'));
    $localidades_activas = split_slug_by_valid_terms($localidad_slug, $localidades_validas);

    $tipos_activos = sanitize_slugs(array_filter(explode('-', $tipo_slug)));
    $antiguedad_activa = sanitize_slugs(array_filter(explode('-', $antiguedad_slug)));

    $ambientes_activa = $ambientes_slug !== '' ? [$ambientes_slug] : [];
    $dormitorios_activa = $dormitorios_slug !== '' ? [$dormitorios_slug] : [];

    $count_operaciones = contar_operaciones($data, $tipos_activos, $localidades_activas);
    $count_tipos = contar_tipos($data);
    $count_antiguedad = contar_antiguedad($data);

    $base_url = [$operacion_slug, $tipo_slug, $ubicacion_slug, $localidades_activas, $antiguedad_activa];

    return [
        'Tipo de operación' => generar_filtros_operacion($count_operaciones, $operacion_slug, $tipo_slug, $ubicacion_slug, $localidades_activas, $antiguedad_activa),
        'Ubicación' => generar_filtros_ubicacion($all_localidades, $localidades_activas, $operacion_slug, $tipo_slug, $ubicacion_slug, $antiguedad_activa),
        'Tipo de propiedad' => generar_filtros_tipo($count_tipos, $tipos_activos, $operacion_slug, $ubicacion_slug, $localidades_activas, $antiguedad_activa),
        'Antiguedad' => generar_filtros_antiguedad($count_antiguedad, $antiguedad_activa, $base_url),
        'Ambientes' => generar_filtros_ambientes($ambientes_activa, $base_url),
        'Dormitorios' => generar_filtros_dormitorios($dormitorios_activa, $base_url, $ambientes_activa)
    ];
}