<?php 
function is_bot(): bool {
    $bots = [
        'Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot', 'Baiduspider', 'YandexBot',
        'Sogou', 'Exabot', 'facebot', 'facebookexternalhit', 'ia_archiver',
        'AhrefsBot', 'SemrushBot', 'MJ12bot', 'DotBot', 'Amazonbot',
        'PetalBot', 'Bytespider', 'GPTBot', 'ClaudeBot'
    ];

    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    foreach ($bots as $bot) {
        if (stripos($agent, $bot) !== false) {
            return true;
        }
    }
    return false;
}


function render_construction_date(string $construction_date): void {

    $today = date('Y-m-d');

    $fecha_formateada = date('d/m/Y', strtotime($construction_date));



    if ($construction_date > $today) {

        echo '<p>' . esc_html($fecha_formateada) . '</p>';

    } else {

        echo '<p>Inmediata</p>';

    }

}



// Estado de construcción

function get_construction_status($status = 4): string {



    $map = [

        2 => 'En pozo',

        4 => 'En construcción',

        6 => 'Terminado'

    ];



    return $map[$status] ?? 'En pozo';



}





function get_full_location($string = ""): string {

    $parts = explode(' | ', $string);

    array_shift($parts);



    // Reemplaza "Centro (Capital Federal)" por "Centro/Microcentro"

    $parts = array_map(function($part) {

        return trim($part) === 'Centro (Capital Federal)' ? 'Centro/Microcentro' : $part;

    }, $parts);



    return implode(', ', $parts);

}



function render_price_format($price = '', $currency = 'USD'): string {

    if (!is_numeric($price)) 

        return '';

    

    $formatted_price = number_format($price, 0, '', '.'); // 170000 → 170.000



    $currency = strtoupper($currency);



    switch ($currency) {

        case 'USD':

            $symbol = 'USD';

            break;

        case 'ARS':

            $symbol = '$';

            break;

        default:

            $symbol = $currency;

    }

    return $formatted_price.' '.$symbol;



}



function render_pagination(string $current_url, int $total_count, int $limit = 12): string {

    $total_pages = (int) ceil($total_count / $limit);

    if ($total_pages <= 1) return '';



    // Detectar current page desde la URL

    $current_page = 1;

    if (preg_match('#/page/(\d+)/?#', $current_url, $matches)) {

        $current_page = max(1, (int)$matches[1]);

    }



    // Limpiar /page/X de la URL

    $base_url = preg_replace('#/page/\d+/?#', '', $current_url);



    // Separar query string

    $query_string = '';

    if (strpos($base_url, '?') !== false) {

        [$base_url, $query_string] = explode('?', $base_url, 2);

        $query_string = '?' . $query_string;

    }



    $html = '<nav class="pagination"><ul>';



    // Prev link

    if ($current_page > 1) {

        $prev_page = $current_page - 1;

        $prev_link = ($prev_page === 1)

            ? $base_url . $query_string

            : $base_url . '/page/' . $prev_page . $query_string;

        $html .= "<li class=\"prev\"><a href=\"$prev_link\"><i class='fa-solid fa-chevron-left'></i></a></li>";

    }



    $num_links_to_show = 5;

    $start_page = max(1, $current_page - floor($num_links_to_show / 2));

    $end_page = min($total_pages, $start_page + $num_links_to_show - 1);

    if (($end_page - $start_page + 1) < $num_links_to_show && $total_pages >= $num_links_to_show) {

        $start_page = $total_pages - $num_links_to_show + 1;

    }

    $start_page = max(1, $start_page);



    for ($i = $start_page; $i <= $end_page; $i++) {

        $link = ($i === 1)

            ? $base_url . $query_string

            : $base_url . '/page/' . $i . $query_string;

        $active = ($i === $current_page) ? ' class="active"' : '';

        $html .= "<li$active><a href=\"$link\">$i</a></li>";

    }



    // Next link

    if ($current_page < $total_pages) {

        $next_page = $current_page + 1;

        $next_link = $base_url . '/page/' . $next_page . $query_string;

        $html .= "<li class=\"next\"><a href=\"$next_link\"><i class='fa-solid fa-chevron-right'></i></a></li>";

    }



    $html .= '</ul></nav>';

    return $html;

}





function slugify($text) {

    $text = mb_convert_encoding(trim($text), 'UTF-8', 'UTF-8'); // limpia y quita espacios

    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

    $text = preg_replace('/[^a-zA-Z0-9\/_|+ -]/', '', $text);

    $text = strtolower(trim($text, '-'));

    $text = preg_replace('/[\/_|+ -]+/', '-', $text);

    return $text;

}



// --- FUNCIONES AUXILIARES GENERALES ---



function sanitize_slugs(array $items): array {

    return array_map(function ($i) {

        $slug = sanitize_title($i);

        return $slug === 'locales'

            ? 'local'

            : $slug;

    }, $items);

}

// --- FUNCION PARA GENERAR URL DE LOS FILTROS ---

function build_filter_url($operacion, $tipo, $ubicacion, $localidades, $antiguedad, $ambientes = [], $dormitorios = []): string {

    $localidades = is_array($localidades) ? $localidades : (empty($localidades) ? [] : [$localidades]);

    $antiguedad = is_array($antiguedad) ? $antiguedad : (empty($antiguedad) ? [] : [$antiguedad]);

    $ambientes = is_array($ambientes) ? $ambientes : (empty($ambientes) ? [] : [$ambientes]);

    $dormitorios = is_array($dormitorios) ? $dormitorios : (empty($dormitorios) ? [] : [$dormitorios]);



    $loc = implode('-', $localidades);

    $ant = implode('-', $antiguedad);



    $base_url = home_url("/aranalfe/propiedades/{$operacion}/{$tipo}/{$ubicacion}/{$loc}/{$ant}");



    // Traer query params actuales

    $query = $_GET;



    // Reemplazar solo los filtros que queremos actualizar

    if (!empty($ambientes)) {

        $query['ambientes'] = $ambientes[0];

    } else {

        unset($query['ambientes']);

    }



    if (!empty($dormitorios)) {

        $query['dormitorios'] = $dormitorios[0];

    } else {

        unset($query['dormitorios']);

    }



    return esc_url(add_query_arg($query, $base_url));

}







function actualizar_query_param($clave, $valor, $multi = false): string {

    $query = $_GET;

    if ($multi) {

        $actual = $query[$clave] ?? [];



        if (!is_array($actual)) {

            $actual = [$actual];

        }



        if (in_array($valor, $actual)) {

            $actual = array_diff($actual, [$valor]);

        } else {

            $actual[] = $valor;

        }



        $actual = array_values($actual);

        unset($query[$clave]);

    } else {

        if (isset($query[$clave]) && $query[$clave] == $valor) {

            unset($query[$clave]);

            $actual = [];

        } else {

            $actual = [$valor];

            unset($query[$clave]);

        }

    }



    $params = [];



    foreach ($query as $k => $v) {

        $params[] = http_build_query([$k => $v]);

    }



    foreach ($actual as $v) {

        if ($v !== '') {

            if ($multi) {

                $params[] = $clave . '[]=' . rawurlencode($v);

            } else {

                $params[] = $clave . '=' . rawurlencode($v);

            }

        }

    }



    $url_base = strtok($_SERVER["REQUEST_URI"], '?');

    $url_base = preg_replace('#/page/\d+/?#', '/', $url_base);

    $url_base = rtrim($url_base, '/');

    if ($url_base === '') $url_base = '/';



    $query_string = implode('&', $params);



    return $url_base . (!empty($query_string) ? '?' . $query_string : '');

}





// --- FILTRO: OPERACIÓN ---

function generar_filtros_operacion(array $data, array $params): array {
    $ventas = ['count' => 0];
    $alquiler = ['count' => 0];
    foreach ($data as $item) {
        if (!isset($item['operation_type'])) continue;
        if ((int)$item['operation_type'] === 1) {
            $ventas = $item;
        } elseif ((int)$item['operation_type'] === 2) {
            $alquiler = $item;
        }
    }



    $params = array_map('intval', $params);
    $checked_ventas = in_array(1, $params);
    $checked_alquiler = in_array(2, $params);
    $url_ventas = actualizar_query_param('operacion', '1', false);
    $url_alquiler = actualizar_query_param('operacion', '2', false);
    return [
        [

            'label' => 'Ventas',

            'checked' => $checked_ventas && !$checked_alquiler || count($params) === 2,

            'url' => $url_ventas

        ],

        [

            'label' => 'Alquiler',

            'checked' => $checked_alquiler && !$checked_ventas || count($params) === 2,

            'url' => $url_alquiler

        ]

    ];

}



















// --- FILTRO: UBICACIÓN ---

function generar_filtros_ubicacion(array $params): array {

    $file_path = get_template_directory() . '/tokko-api/data/locations.json';

    if (!file_exists($file_path)) return [];

    $json = file_get_contents($file_path);

    $data = json_decode($json, true);



    if (empty($data) || !is_array($data)) return [];

    $out = [];



    foreach ($data as $loc) {

        $label = $loc['location_name'] ?? '';

        if($label = $loc['location_name'] === 'Centro (Capital Federal)'){

            $label = $loc['location_name'] = 'Centro/Microcentro';

        }else{

            $label = $loc['location_name'];

        }

        $id = (int) ($loc['location_id'] ?? 0);



        if (!$id) continue;



        $checked = in_array($id, $params, true);

        $url = actualizar_query_param('localidad', $id, true);



        $out[] = [

            'label' => $label,

            'checked' => $checked,

            'url' => $url

        ];

    }



    return $out;

}





//  FILTRO TYPE PROPERTY

function generar_filtros_tipo(array $data, array $params): array {
    $out = [];
    $property_types = get_only_property_types();
  
    foreach ($property_types as $type) {
        $label = $type['type']; // o 'tag_name' según tu estructura
        $count = $type['count'];
        $id = (int) $type['id']; // o 'tag_id' según tu estructura

        $checked = in_array($id, $params, true);
        $url = actualizar_query_param('tipo', $id, true); // true => multiple opción

        $out[] = [
            'label' => $label,
            'checked' => $checked,
            'url' => $url
        ];
    }

    return $out;
}







function generar_filtros_antiguedad(array $data, array $params): array {

    if (empty($data)) {

        return [];

    }

    $agrupados = [

        'en-construccion' => ['label' => 'En Construcción', 'count' => 0],

        'a-estrenar' => ['label' => 'A estrenar', 'count' => 0],

        'terminado' => ['label' => 'Terminados', 'count' => 0],

    ];



    foreach ($data as $type) {

        $amount = (int) $type['amount'];

        $count = (int) $type['count'];



        if ($amount === -1) {

            $agrupados['en-construccion']['count'] += $count;

        } elseif ($amount === 0) {

            $agrupados['a-estrenar']['count'] += $count;

        } else {

            $agrupados['terminado']['count'] += $count;

        }

    }



    $out = [];



    foreach ($agrupados as $id => $info) {

        if ($info['count'] === 0) continue; // ocultar si no hay resultados



        $checked = in_array($id, $params, true);

        $url = actualizar_query_param('antiguedad', $id, true);



        $out[] = [

            'label' => $info['label'] . ' (' . $info['count'] . ')',

            'checked' => $checked,

            'url' => $url

        ];

    }



    return $out;

}





// ---- FILTRO: AMBINTES --

function generar_filtros_ambientes(array $data, int $params): array {

    // if (empty($data)) {

    //     return [];

    // }

    $agrupados = [

        '1' => ['label' => '1', 'count' => 0],

        '2' => ['label' => '2', 'count' => 0],

        '3' => ['label' => '3', 'count' => 0],

        '4' => ['label' => '4+', 'count' => 0],

    ];

    foreach ($data as $type) {

        $amount = (int) $type['amount'];

        $count = (int) $type['count'];

  

        if ($amount === 1) {

            $agrupados['1']['count'] += $count;

        } elseif ($amount === 2) {

            $agrupados['2']['count'] += $count;

        } elseif($amount === 3) {

            $agrupados['3']['count'] += $count;

        } else if($amount > 3){

            $agrupados['4']['count'] += $count;

        }

    }

    $out = [];

 

    foreach ($agrupados as $id => $info) {

        // if ($info['count'] === 0) continue; // ocultar si no hay resultados



        $checked = ((int)$params === (int)$id);

        $url = actualizar_query_param('ambientes', $id);



        $out[] = [

            'label' => $info['label'],

            'checked' => $checked,

            'url' => $url

        ];

    }



    return $out;

}

// FILTROS DORMITORIOS

function generar_filtros_dormitorios(array $data, int $params) : array {

    // if (empty($data)) {

    //     return [];

    // }

    $agrupados = [

        '1' => ['label' => '1', 'count' => 0],

        '2' => ['label' => '2', 'count' => 0],

        '3' => ['label' => '3', 'count' => 0],

        '4' => ['label' => '4+', 'count' => 0],

    ];



    foreach ($data as $type) {

        $amount = (int) $type['amount'];

        $count = (int) $type['count'];

  

        if ($amount === 1) {

            $agrupados['1']['count'] += $count;

        } elseif ($amount === 2) {

            $agrupados['2']['count'] += $count;

        } elseif($amount === 3) {

            $agrupados['3']['count'] += $count;

        } else if($amount > 3){

            $agrupados['4']['count'] += $count;

        }

    }

    $out = [];

 

    foreach ($agrupados as $id => $info) {

        // if ($info['count'] === 0) continue; // ocultar si no hay resultados



        $checked = ((int)$params === (int)$id);

        $url = actualizar_query_param('dormitorio', $id);



        $out[] = [

            'label' => $info['label'],

            'checked' => $checked,

            'url' => $url

        ];

    }



    return $out;

}





function generar_filtros_servicios(array $data, array $params) {

    $out = [];

    foreach ($data as $type) {

        $label = $type['tag_name'];

        $count = $type['count'];

        $id = (int) $type['tag_id'];



        $checked = in_array($id, $params, true);

        $url = actualizar_query_param('servicios', $id, true);



        $out[] = [

            'label' => $label . ' (' . $count . ')',

            'checked' => $checked,

            'url' => $url

        ];

    }

    return $out;

}



function generar_filtros_otros_servicios(array $data, array $params){

    $out = [];

    foreach ($data as $type) {

        $label = $type['tag_name'];

        $count = $type['count'];

        $id = (int) $type['tag_id'];

        $checked = in_array($id, $params, true);

        $url = actualizar_query_param('otros', $id, true);

        $out[] = [

            'label' => $label . ' (' . $count . ')',

            'checked' => $checked,

            'url' => $url

        ];

    }

    return $out;

}









// AJUSTE URL VIDEO

function fix_youtube_embed_url($url) {

    // Si no es una URL embed, no hacemos nada

    if (strpos($url, 'youtube.com/embed/') === false) {

        return $url;

    }



    // Buscar si tiene t= (en segundos o tipo 1m30s)

    if (preg_match('/[?&]t=([\dhms]+)/', $url, $match)) {

        $time = $match[1];



        // Convertir a segundos si es tipo 1m30s

        if (preg_match('/(\d+)m(\d+)s/', $time, $parts)) {

            $seconds = ($parts[1] * 60) + $parts[2];

        } elseif (preg_match('/(\d+)s/', $time, $parts)) {

            $seconds = $parts[1];

        } else {

            $seconds = (int)$time;

        }



        // Eliminar t= del original

        $url = preg_replace('/[?&]t=[\dhms]+/', '', $url);



        // Reemplazar ? por ?start= o agregar &start= según corresponda

        $url .= (strpos($url, '?') === false ? '?' : '&') . 'start=' . $seconds;

    }



    return $url;

}



function get_images_details_property_from_data(array $propertyData): array {
    $photos = $propertyData['objects'][0]['photos'] ?? [];

    usort($photos, function($a, $b) {
        return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
    });

    return $photos;
}



function get_images_development(array $data): array {
    $photos = $data['photos'] ?? [];
    usort($photos, function($a, $b) {
        return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
    });

    return $photos;

}

function get_images_blue_print(array $data) {
    $photos = $data['photos'] ?? [];

    // Filtrar solo los que tengan is_blueprint en true
    $photos = array_filter($photos, function($photo) {
        return $photo['is_blueprint'] ?? false;
    });

    // Ordenar por el campo 'order'
    usort($photos, function($a, $b) {
        return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
    });

    return $photos;
}



function get_hero_image_development(array $data): ?array {

    $photos = $data['photos'] ?? [];

    foreach ($photos as $photo) {

        if (!empty($photo['is_front_cover'])) {

            return $photo;

        }

    }

    return null;

}



function extractFloorIdentifier(string $text): ?string {

    $pattern = '/Piso\s(\d+)/';

    // Intenta encontrar el patrón en el string

    if (preg_match($pattern, $text, $matches)) {

        // Si se encuentra una coincidencia, el primer grupo capturado ($matches[1])

        // contiene el identificador de piso.

        return $matches[1];

    } else {

        // Si no se encuentra el patrón, retorna null.

        return null;

    }

}



function get_min_max_price(array $unidades_data): array {

    $all_global_prices_by_currency = [];

    // Verificar si 'objects' existe y es un array antes de intentar iterar

    if (isset($unidades_data['objects']) && is_array($unidades_data['objects'])) {

        foreach ($unidades_data['objects'] as $unidad) {

            // Verificar si 'operations' existe y es un array

            if (isset($unidad['operations']) && is_array($unidad['operations'])) {

                foreach ($unidad['operations'] as $operation) {

                    // Verificar si 'prices' existe y es un array dentro de la operación

                    if (isset($operation['prices']) && is_array($operation['prices'])) {

                        foreach ($operation['prices'] as $price_data) {

                            // Verificar si 'price' y 'currency' existen y 'price' es numérico

                            if (isset($price_data['price']) && is_numeric($price_data['price']) && isset($price_data['currency'])) {

                                $currency = $price_data['currency'];

                                $price = $price_data['price'];

                                // Asegurarse de que el array para esta moneda exista

                                if (!isset($all_global_prices_by_currency[$currency])) {

                                    $all_global_prices_by_currency[$currency] = [];

                                }

                                $all_global_prices_by_currency[$currency][] = $price;

                            }

                        }

                    }

                }

            }

        }

    }

    $result = [];

    foreach ($all_global_prices_by_currency as $currency => $prices_list) {

        if (!empty($prices_list)) {

            $result['prices'] = [

                

                'min_price' => render_price_format(min($prices_list), $currency ) ,

                'max_price' => render_price_format(max($prices_list), $currency ) 

            ];

        }

    }

    return $result;

}



function ids_from_query($query) {

    $ids = [];

    if (isset($_GET[$query])) {

        $raw = sanitize_text_field($_GET[$query]);

        $parts = explode(',', $raw);

        foreach ($parts as $id) {

            $id = trim($id);

            if (is_numeric($id)) {

                $ids[] = $id;

            }

        }

    }

    return $ids;

}



function build_url_with_order(string $order_by, string $order): string {

    $params = $_GET;

    // Limpiar valores anteriores

    unset($params['order_by'], $params['order']);

    // Agregar los nuevos

    $params['order_by'] = $order_by;

    $params['order'] = $order;

    // Armar base URL sin query

    $base_url = strtok($_SERVER['REQUEST_URI'], '?');

    // Devolver URL con nuevos params

    return $base_url . '?' . http_build_query($params);

}



function obtener_locacion_por_id(array $ids = [], $locations): array {
    $ids = is_array($ids) ? $ids : [$ids];
    $result = [];

    foreach ($locations as $location) {
        if (!is_array($location)) {
            continue; // salta los valores que no sean arrays
        }

        if (isset($location['id']) && in_array((int)$location['id'], $ids, true)) {
            $result[] = $location;
        }
    }

    return $result;
}



function obtener_propiedad_por_id(array $ids, array $locations): array {
    $ids = is_array($ids) ? $ids : [$ids];
    $result = [];

    foreach ($locations as $location) {
        if (in_array((int)$location['id'], $ids, true)) {
            $result[] = $location;
        }
    }
    return $result;
}



function return_url (){

    $base = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

    $domain = '';

    if($base == 'https://test.zetenta.com'){

        $domain = '/aranalfe';

    }else{

        $domain = '';

    }

    return $domain;

}



function render_order_options($current_order_by, $current_order) {

    $options = [

        ['label' => 'Más reciente', 'order_by' => 'created_at', 'order' => 'DESC'],

        ['label' => 'Precio más bajo', 'order_by' => 'price', 'order' => 'ASC'],

        ['label' => 'Precio más alto', 'order_by' => 'price', 'order' => 'DESC'],

    ];



    usort($options, function($a, $b) use ($current_order_by, $current_order) {

        if ($a['order_by'] === $current_order_by && $a['order'] === $current_order) return -1;

        if ($b['order_by'] === $current_order_by && $b['order'] === $current_order) return 1;

        return 0;

    });



    foreach ($options as $opt) {

        $url = build_url_with_order($opt['order_by'], $opt['order']);

        echo '<li class="options-list-select option-order">';

        echo '<a href="' . $url . '"><p>' . $opt['label'] . '</p></a>';

        echo '</li>';

    }

}


function get_status_property(int $status): string {
    switch ($status) {
        case 1:
            return 'A cotizar';
        case 2:
            return 'Disponible';
        case 3:
            return 'Reservada';
        case 4:
            return 'No disponible';
        default:
            return 'Estado desconocido';
    }
}

function get_object_position(string $acf = ''): string {
   
    $position = strtolower(trim($acf));


    return 'style="object-position: ' . esc_attr($position) . ';"';
}

function render_filters_apply($data, $barrios) {
    if (empty($data) || empty($barrios)) return '';

    $output = '';
    $current_url = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($current_url);
    parse_str($parsed_url['query'] ?? '', $query_params);

    foreach ($data as $id) {
        foreach ($barrios as $barrio) {
            if ((string)$id === $barrio['id']) {
                // Remover solo ese ID del array de localidad
                $modified_params = $query_params;
                if (isset($modified_params['localidad'])) {
                    $modified_params['localidad'] = array_filter(
                        $modified_params['localidad'],
                        fn($val) => (string)$val !== (string)$id
                    );
                }

                // Reconstruir URL
                $base = $parsed_url['path'];
                $query_string = http_build_query($modified_params);
                $url = $base . ($query_string ? '?' . $query_string : '');

                $output .= '<a href="' . esc_url($url) . '" class="filter-tag">' . esc_html($barrio['name']) . ' &times;</a> ';
                break;
            }
        }
    }

    
    $no_localidad = $query_params;
    unset($no_localidad['localidad']);
    $clear_url = $parsed_url['path'] . (http_build_query($no_localidad) ? '?' . http_build_query($no_localidad) : '');
    $output .= '<a class="label-filter" href="' . esc_url($clear_url) . '" class="filter-clear">Borrar filtros</a>';
    return '<div class="filter-wrap">' . $output . '</div>';
}

function render_property_title($params, $ubicacion_localidad_slug, $barrios, $type_property_slug, $dataFilter) {
    $locations_texts = [];
    $property_texts = [];

    $hasOperacion = !empty($params['operacion']);
    $hasLocalidad = !empty($params['localidad']);
    $hasTipo = !empty($params['tipo']);

    if ($hasLocalidad) {
        $locations_texts = obtener_locacion_por_id($ubicacion_localidad_slug, $barrios);
    }

    if ($hasTipo) {
        $property_texts = obtener_propiedad_por_id($type_property_slug, $dataFilter['property_types'] ?? []);
    }

    $operacion_label = null;
    if ($hasOperacion) {
        if (is_array($params['operacion'])) {
            $operacion_label = null;
        } else {
            $operacion_label = $params['operacion'] === '1' ? 'Venta' : 'Alquiler';
        }
    }

    echo '<h1>';

    if (!$hasOperacion && !$hasLocalidad && !$hasTipo) {
        // Ningún filtro, h1 vacío
        echo 'Todas las propiedades disponibles';
    } else {
        if (!$operacion_label) {
            if ($property_texts) {
                $types = array_map(fn($p) => esc_html($p['type']), $property_texts);
                $types_unique = array_unique($types);
                $types_text = implode(', ', $types_unique);
                echo  $types_text . ' disponible';
            } else {
                echo 'Todas las propiedades disponibles';
            }
        } else {
            if ($property_texts) {
                foreach ($property_texts as $prop) {
                    echo esc_html($prop['type']) . ' ';
                }
            } else {
                echo 'Propiedades ';
            }
            echo 'en ' . esc_html($operacion_label);
        }

        if ($locations_texts) {
            echo ' en ';
            $total = count($locations_texts);
            foreach ($locations_texts as $i => $loc) {
                echo esc_html($loc['name']);
                if ($i < $total - 1) {
                    echo ', ';
                }
            }
        }
    }

    echo '</h1>';
}