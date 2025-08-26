<?php 
function is_bot(): bool {
    // Lista de bots buenos que NO deberían usar cache
    $good_bots = [
        'Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot'
    ];

    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip    = $_SERVER['REMOTE_ADDR'] ?? '';

    // Si la IP comienza con 66.249 → es bot
    if (strpos($ip, '66.249') === 0) {
        return true;
    }

    // Revisar user-agent
    foreach ($good_bots as $bot) {
        if (stripos($agent, $bot) !== false) {
            return false; // Bot bueno
        }
    }

    // Si contiene "bot", "spider" o "crawler" → bot malo
    if (preg_match('/bot|spider|crawler/i', $agent)) {
        return true;
    }

    return false; // Usuario normal
}

function ip_es_arg_uy(string $ip): bool {
    $cache_key = 'geoip_' . md5($ip);
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $url = "http://ip-api.com/json/{$ip}?fields=status,countryCode";
    $response = @file_get_contents($url);
    if ($response === false) {
        // No se pudo geolocalizar, bloqueamos por seguridad
        set_transient($cache_key, false, 86400);
        return false;
    }

    $data = json_decode($response, true);
    if (!isset($data['status']) || $data['status'] !== 'success') {
        set_transient($cache_key, false, 86400);
        return false;
    }

    $allowed = in_array($data['countryCode'], ['AR', 'UY']);
    set_transient($cache_key, $allowed, 86400); // cache 1 día
    return $allowed;
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

    // Separar path y query string
    $parsed_url = parse_url($current_url);
    $path = rtrim($parsed_url['path'] ?? '', '/');
    $query_string = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';

    // Detectar current page
    $current_page = 1;
    if (preg_match('#/page/(\d+)$#', $path, $matches)) {
        $current_page = max(1, (int)$matches[1]);
        // Limpiar /page/X
        $base_path = preg_replace('#/page/\d+$#', '', $path);
    } else {
        $base_path = $path;
    }

    $html = '<nav class="pagination"><ul>';

    // Prev link
    if ($current_page > 1) {
        $prev_page = $current_page - 1;
        $prev_link = ($prev_page === 1)
            ? $base_path . '/' . $query_string
            : $base_path . '/page/' . $prev_page . '/' . $query_string;
        $html .= "<li class=\"prev\"><a href=\"$prev_link\"><i class='fa-solid fa-chevron-left'></i></a></li>";
    }

    // Rango de páginas
    $num_links_to_show = 5;
    $start_page = max(1, $current_page - floor($num_links_to_show / 2));
    $end_page = min($total_pages, $start_page + $num_links_to_show - 1);
    if (($end_page - $start_page + 1) < $num_links_to_show && $total_pages >= $num_links_to_show) {
        $start_page = $total_pages - $num_links_to_show + 1;
    }
    $start_page = max(1, $start_page);

    for ($i = $start_page; $i <= $end_page; $i++) {
        $link = ($i === 1)
            ? $base_path . '/' . $query_string
            : $base_path . '/page/' . $i . '/' . $query_string;

        $link_for_compare = rtrim(parse_url($link, PHP_URL_PATH), '/');
        $current_path = rtrim($path, '/');

        $is_active = ($link_for_compare === $current_path) ? ' class="active"' : '';

        $html .= "<li$is_active><a href=\"$link\">$i</a></li>";
    }

    // Next link
    if ($current_page < $total_pages) {
        $next_page = $current_page + 1;
        $next_link = $base_path . '/page/' . $next_page . '/' . $query_string;
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

function generar_filtros_operacion(array $params): array {
  
    $params = array_map('intval', $params); // asegurar enteros
    $operaciones = [
        1 => 'Ventas',
        2 => 'Alquiler'
    ];

    $out = [];
    foreach ($operaciones as $id => $label) {
        $checked = !empty($params) && in_array($id, $params, true); // solo marcar si hay params
        $url = actualizar_query_param('operacion', $id, true); // true => multiple opción

        $out[] = [
            'label' => $label,
            'checked' => $checked,
            'url' => $url,
            'param' => 'operacion',
            'value' => $id
        ];
    }

    return $out;
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
            'url' => $url,
            'param' => 'localidad',
            'value' => $id
        ];
    }



    return $out;

}





//  FILTRO TYPE PROPERTY

function generar_filtros_tipo(array $params): array {
    $file_path = get_template_directory() . '/tokko-api/data/property.json';
    if (!file_exists($file_path)) return [];
    $json = file_get_contents($file_path);
    $property_types = json_decode($json, true);
    if (empty($property_types) || !is_array($property_types)) return [];
    $out = [];
    foreach ($property_types as $type) {
        $label = $type['type']; // o 'tag_name' según tu estructura
        $count = $type['count'];
        $id = (int) $type['id']; // o 'tag_id' según tu estructura

        $checked = in_array($id, $params, true);
        $url = actualizar_query_param('tipo', $id, true); // true => multiple opción

        $out[] = [
            'label' => $label,
            'checked' => $checked,
            'url' => $url,
            'param' => 'tipo',
            'value' => $id
        ];
    }

    return $out;
}



function generar_filtros_antiguedad(array $params): array {
    $agrupados = [
        'en-construccion' => 'En Construcción',
        'a-estrenar' => 'A estrenar',
        'terminado' => 'Terminados',
    ];

    $out = [];

    foreach ($agrupados as $id => $label) {
        $checked = in_array($id, $params, true);
        $url = actualizar_query_param('antiguedad', $id, true);

        $out[] = [
            'label' => $label,
            'checked' => $checked,
            'url' => $url,
            'param' => 'antiguedad',
            'value' => $id
        ];
    }

    return $out;
}





// ---- FILTRO: AMBIENTES --
function generar_filtros_ambientes(array $params): array {
    $params = array_map('intval', $params); // asegurarse que sean ints
    $agrupados = [
        '1' => ['label' => '1', 'count' => 0],
        '2' => ['label' => '2', 'count' => 0],
        '3' => ['label' => '3', 'count' => 0],
        '4' => ['label' => '4+', 'count' => 0],
    ];
    $out = [];
    foreach ($agrupados as $id => $info) {
        $checked = in_array((int)$id, $params, true);
        $url = actualizar_query_param('ambientes', $id, true); // selección múltiple

        $out[] = [
            'label'   => $info['label'],
            'count'   => $info['count'],
            'checked' => $checked,
            'url'     => $url,
            'param' => 'ambientes',
            'value' => $info['label']
        ];
    }

    return $out;
}

// FILTROS DORMITORIOS

function generar_filtros_dormitorios(array $params): array {
    $params = array_map('intval', (array) $params); // siempre array de ints

    $agrupados = [
        '1' => ['label' => '1',   'count' => 0],
        '2' => ['label' => '2',   'count' => 0],
        '3' => ['label' => '3',   'count' => 0],
        '4' => ['label' => '4+',  'count' => 0],
    ];

    $out = [];
    foreach ($agrupados as $id => $info) {
        $checked = in_array((int)$id, $params, true);
        $url = actualizar_query_param('dormitorio', $id, true);

        $out[] = [
            'label'   => $info['label'],
            'count'   => $info['count'],
            'checked' => $checked,
            'url'     => $url,
            'param' => 'dormitorio',
            'value' => $info['label']
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
    unset($params['order_by'], $params['order']);

    $queryParts = [];

    foreach ($params as $key => $value) {
        if (is_array($value)) {
            foreach ($value as $v) {
                $queryParts[] = urlencode($key) . '[]=' . urlencode($v);
            }
        } else {
            $queryParts[] = urlencode($key) . '=' . urlencode($value);
        }
    }

    $queryParts[] = 'order_by=' . urlencode($order_by);
    $queryParts[] = 'order=' . urlencode($order);

    $base_url = strtok($_SERVER['REQUEST_URI'], '?');

    return $base_url . '?' . implode('&', $queryParts);
}


function obtener_locacion_por_id(array $locations, array $ids = []): array {
    $ids = is_array($ids) ? $ids : [$ids];
    $result = [];

    foreach ($locations as $location) {
        if (!is_array($location)) continue;
        if (isset($location['id']) && in_array((int)$location['id'], $ids, true)) {
            $result[] = $location;
        }
    }

    return $result;
}



function obtener_propiedad_por_id(array $ids): array {
    $ids = is_array($ids) ? $ids : [$ids];
    $result = [];
    $file_path = get_template_directory() . '/tokko-api/data/property.json';
    if (!file_exists($file_path)) return [];
    $json = file_get_contents($file_path);
    $property_types = json_decode($json, true);
    if (empty($property_types) || !is_array($property_types)) return [];

    foreach ($property_types as $property) {
        if (in_array((int)$property['id'], $ids, true)) {
            $result[] = $property;
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

    // Forzar que $data sea array para evitar warning
    if (!is_array($data)) {
        $data = [(string) $data];
    }

    // Si $data contiene solo un valor vacío o "1", no lo tomamos como localidad válida
    $localidades_activas = array_filter($data, fn($val) => (string)trim($val) !== '' && (string)$val !== '1');
    if (empty($localidades_activas)) return '';

    $output = '';
    $current_url = $_SERVER['REQUEST_URI'];
    $parsed_url = parse_url($current_url);
    parse_str($parsed_url['query'] ?? '', $query_params);

    foreach ($data as $id) {
        foreach ($barrios as $barrio) {
            if ((string)$id === $barrio['id']) {
                $modified_params = $query_params;

                if (isset($modified_params['localidad'])) {
                    $localidad_param = $modified_params['localidad'];
                    if (!is_array($localidad_param)) {
                        $localidad_param = [(string)$localidad_param];
                    }

                    $modified_params['localidad'] = array_filter(
                        $localidad_param,
                        fn($val) => (string)$val !== (string)$id
                    );

                    if (empty($modified_params['localidad'])) {
                        unset($modified_params['localidad']);
                    }
                }

                $base = $parsed_url['path'];
                $query_string = http_build_query($modified_params);
                $url = $base . ($query_string ? '?' . $query_string : '');

                $output .= '<a href="' . esc_url($url) . '" class="filter-tag">' . esc_html($barrio['name']) . ' &times;</a> ';
                break;
            }
        }
    }

    // Si hay localidades activas, mostrar botón "Borrar filtros"
    if (!empty($localidades_activas)) {
        $no_localidad = $query_params;
        unset($no_localidad['localidad']);
        $clear_url = $parsed_url['path'] . (http_build_query($no_localidad) ? '?' . http_build_query($no_localidad) : '');
        $output .= '<a class="label-filter" href="' . esc_url($clear_url) . '" class="filter-clear">Borrar filtros</a>';
    }

    return '<div class="filter-wrap">' . $output . '</div>';
}


function render_property_title($params, $ubicacion_localidad_slug, $barrios, $type_property_slug) {
    $locations_texts = [];
    $property_texts = [];

    $hasOperacion = !empty($params['operacion']);
    $hasLocalidad = !empty($params['localidad']);
    $hasTipo = !empty($params['tipo']);

    if ($hasLocalidad) {
        $locations_texts = obtener_locacion_por_id($ubicacion_localidad_slug, $barrios);
    }

    if ($hasTipo) {
        $property_texts = obtener_propiedad_por_id($type_property_slug);
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

function get_feature_video(array $data): array {
    if (empty($data)) {
        return [];
    }

    // Filtrar por título que contenga comillas
    $filtered = array_filter($data, function($video) {
        return strpos($video['title'], '"') !== false;
    });

    // Usar filtrados si existen, si no usar todos
    $videosToSearch = !empty($filtered) ? $filtered : $data;

    // Ordenar por 'order'
    usort($videosToSearch, function($a, $b) {
        return $a['order'] <=> $b['order'];
    });

    // Retornar solo el primero
    return reset($videosToSearch);
}

function sort_prices_by_operation(array $data): array {
    // Definir un orden de prioridad para los tipos de operación.
    $orden_prioridad = ['Venta' => 1, 'Alquiler' => 2];

    // Usar usort para ordenar el array.
    usort($data, function ($a, $b) use ($orden_prioridad) {
        $tipo_a = $a['operation_type'] ?? '';
        $tipo_b = $b['operation_type'] ?? '';

        $prioridad_a = $orden_prioridad[$tipo_a] ?? 99;
        $prioridad_b = $orden_prioridad[$tipo_b] ?? 99;

        // Comparar prioridades.
        if ($prioridad_a === $prioridad_b) {
            return 0; // Mantener el orden relativo si las prioridades son iguales.
        }

        return $prioridad_a <=> $prioridad_b;
    });

    return $data;
}

function render_all_prices_formatted(array $operations): array
{
    $formatted_prices = [];

    foreach ($operations as $operation) {
        $operation_type = $operation['operation_type'] ?? 'Desconocido';
        $prices = $operation['prices'] ?? [];

        foreach ($prices as $price_data) {
            $price = $price_data['price'] ?? 0;
            $currency = $price_data['currency'] ?? '';

            if ($price > 0 && !empty($currency)) {
                $formatted_prices[] = [
                    'operation_type' => $operation_type,
                    'formatted_price' => render_price_format($price, $currency)
                ];
            }
        }
    }

    return $formatted_prices;
}