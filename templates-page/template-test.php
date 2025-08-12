<?php
/**
 * Template Name: TEST
 */
get_header();

// Armar el array "data"
$data = [
    'current_localization_id' => 1,
    'current_localization_type' => 'country',
    'price_from' => 1,
    'price_to' => 999999999,
    'operation_types' => [1, 2],
    'property_types' => [2], // o range(1, 28)
    'currency' => 'USD',
    'filters' => [],
    'with_tags' => [],
    'without_tags' => []
];

// Serializar el array en JSON y luego urlencode
$data_json = urlencode(json_encode($data, JSON_UNESCAPED_SLASHES));

// Armar la URL completa como en Postman
$url = 'https://www.tokkobroker.com/api/v1/property/search/?'
     . 'lang=es_ar'
     . '&format=json'
     . '&limit=12'
     . '&offset=0'
     . '&order_by=created_at'
     . '&order=desc'
     . '&data=' . $data_json
     . '&key=fad0d191d200804e836be0b26626ac919fa37e8a';

// Obtener respuesta simple
$response = file_get_contents($url);

// Decodificar JSON
$result = json_decode($response, true);

// Mostrar propiedades
if (!empty($result['objects'])) {
    foreach ($result['objects'] as $propiedad) {
        echo '<pre style="color: black;">';
        print_r($propiedad); // Reemplazar con tu HTML
        echo '</pre>';
    }
} else {
    echo 'No se encontraron propiedades. 333';
}

get_footer();