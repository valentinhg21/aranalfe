<?php 



// Estado de construcción
function get_construction_status($status = 4): string {

    $map = [
        2 => 'En pozo',
        4 => 'En construcción',
        6 => 'Finalizado'
    ];

    return $map[$status] ?? 'Estado desconocido';

}


function get_full_location($string = ""): string {

    $parts = explode(' | ', $string);

    // Elimina el primero (Argentina) y une el resto

    array_shift($parts);

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

    $total_pages = ceil($total_count / $limit);

    if ($total_pages <= 1) 
        return '';
    
    // Detectar página actual

    $current_page = 1;

    if (preg_match('#/page/(\d+)/?#', $current_url, $matches)) {

        $current_page = max(1, intval($matches[1]));

    }

    // Base URL sin /page/X

    $base_url = preg_replace('#/page/\d+/?#', '', rtrim($current_url, '/'));

    $html = '<nav class="pagination"><ul>';

    // Prev link

    if ($current_page > 1) {

        $prev_page = $current_page - 1;

        $prev_link = ($prev_page === 1)
            ? $base_url
            : $base_url.'/page/'.$prev_page;

        $html.= "<li class=\"prev\"><a href=\"$prev_link\"><i class='fa-solid fa-chevron-left'>" +
                "</i></a></li>";

    }

    // Page links

    for ($i = 1; $i <= $total_pages; $i++) {

        $link = ($i === 1)
            ? $base_url
            : $base_url.'/page/'.$i;

        $active = ($i === $current_page)
            ? ' class="active"'
            : '';

        $html.= "<li$active><a href=\"$link\">$i</a></li>";

    }

    // Next link

    if ($current_page < $total_pages) {

        $next_page = $current_page + 1;

        $next_link = $base_url.'/page/'.$next_page;

        $html.= "<li class=\"next\"><a href=\"$next_link\"><i class='fa-solid fa-chevron-right'" +
                "></i></a></li>";

    }

    $html.= '</ul></nav>';

    return $html;

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

    $base_url = home_url("/propiedades/{$operacion}/{$tipo}/{$ubicacion}/{$loc}/{$ant}");

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

function actualizar_query_param($clave, $valor): string {
    // Copiamos los parámetros actuales
    $query = $_GET;

    // Seteamos o quitamos el parámetro
    if ($valor === null || $valor === '') {
        unset($query[$clave]);
    } else {
        $query[$clave] = $valor;
    }

    // Reconstruimos la URL actual sin modificar la base
    $url_base = strtok($_SERVER["REQUEST_URI"], '?');
    $query_string = http_build_query($query);

    return esc_url($url_base . (!empty($query_string) ? '?' . $query_string : ''));
}

// --- FILTRO: OPERACIÓN ---

function generar_filtros_operacion(
    array $count_operaciones,
    $operacion_slug,
    $tipo_slug,
    $ubicacion_slug,
    array $localidades_activas,
    array $antiguedad_activa
): array {

    return [

        [

            'label' => 'Ventas ('
                .$count_operaciones['venta']
                .')',

            'checked' => ($operacion_slug === 'venta' || $operacion_slug === 'ventas'),

            'url' => build_filter_url(
                'venta',
                $tipo_slug,
                $ubicacion_slug,
                $localidades_activas,
                $antiguedad_activa
            )

        ],

        [

            'label' => 'Alquiler ('
                .$count_operaciones['alquiler']
                .')',

            'checked' => ($operacion_slug === 'alquiler'),

            'url' => build_filter_url(
                'alquiler',
                $tipo_slug,
                $ubicacion_slug,
                $localidades_activas,
                $antiguedad_activa
            )

        ]
    ];

}

function contar_operaciones(array $data, array $tipos_activos, array $localidades_activas): array {

    $count = [
        'venta' => 0,
        'alquiler' => 0
    ];

    // Normalizar tipos activos (ej: "locales" -> "local")
    $tipos_activos_normalizados = array_map(
        fn($t) => rtrim(sanitize_title($t), 's'),
        $tipos_activos
    );

    foreach($data as $prop) {

        $op = strtolower($prop['operations'][0]['operation_type'] ?? '');

        // Normalizar tipo
        $tipo = rtrim(sanitize_title($prop['type']['name'] ?? ''), 's');

        $loc = get_full_location($prop['location']['full_location'] ?? '');
        $slugs = sanitize_slugs(array_map('trim', explode(',', $loc)));

        $valido_tipo = empty($tipos_activos) || in_array(
            $tipo,
            $tipos_activos_normalizados
        );
        $valido_loc = empty($localidades_activas) || array_intersect(
            $localidades_activas,
            $slugs
        );

        if ($valido_tipo && $valido_loc && isset($count[$op])) {
            $count[$op]++;
        }
    }

    return $count;
}

// --- FILTRO: TIPO DE PROPIEDAD ---

function contar_tipos(array $data): array {

    $tipos = [];

    foreach($data as $prop) {
        $tipo = $prop['type']['name'] ?? '';
        $tipo = ($tipo === 'Local')
            ? 'Locales'
            : $tipo;
        if ($tipo) 
            $tipos[$tipo] = ($tipos[$tipo] ?? 0) + 1;
        }
    
    return $tipos;

}

function generar_filtros_tipo(
    array $count_tipos,
    array $tipos_activos,
    $operacion_slug,
    $ubicacion_slug,
    array $localidades_activas,
    array $antiguedad_activa
): array {

    $out = [];

    foreach($count_tipos as $tipo => $count) {

        $slug = sanitize_title($tipo);

        $checked = in_array($slug, $tipos_activos);

        $nuevos = $checked
            ? array_filter($tipos_activos, fn($t) => $t !== $slug)
            : [
                ...$tipos_activos,
                $slug
            ];

        $url = build_filter_url(
            $operacion_slug,
            implode('-', $nuevos),
            $ubicacion_slug,
            $localidades_activas,
            $antiguedad_activa
        );

        $out[] = [

            'label' => $tipo.' ('.$count.')',

            'checked' => $checked,

            'url' => $url
        ];

    }

    return $out;

}

// --- FILTRO: UBICACIÓN ---

function generar_filtros_ubicacion(
    array $all_localidades,
    array $localidades_activas,
    $operacion_slug,
    $tipo_slug,
    $ubicacion_slug,
    array $antiguedad_activa
): array {

    $out = [];

    foreach($all_localidades as $loc) {

        $label = $loc['location_name'];

        $slug = sanitize_title($label);

        $checked = in_array($slug, $localidades_activas);

        $nuevas = $checked
            ? array_filter($localidades_activas, fn($l) => $l !== $slug)
            : [
                ...$localidades_activas,
                $slug
            ];

        $url = build_filter_url(
            $operacion_slug,
            $tipo_slug,
            $ubicacion_slug,
            $nuevas,
            $antiguedad_activa
        );

        $out[] = [

            'label' => $label,

            'checked' => $checked,

            'url' => $url
        ];

    }

    return $out;

}

// --- FILTRO: ANTIGÜEDAD ---
function contar_antiguedad(array $data): array {

    $cuenta = [
        'terminado' => 0,
        'en_construccion' => 0
    ];

    foreach($data as $prop) {
        $status = $prop['development']['construction_status'] ?? null;
        $age = $prop['age'] ?? null;
        // if ($status !== null) {     if ($status == 4) $cuenta['en_construccion']++;
        // elseif ($status == 6) $cuenta['terminado']++; } else {
        if ($age == -1) 
            $cuenta['en_construccion']++;
        elseif($age >= 0)$cuenta['terminado']++;
        // }
    }

    return $cuenta;

}

function generar_filtros_antiguedad(array $cuentas, array $seleccionadas, array $base_url_parts): array {

    $tipos = [

        'terminado' => 'Terminados',

        'en_construccion' => 'En Construcción'

    ];

    $out = [];

    foreach($tipos as $key => $label) {

        $activos = $seleccionadas;

        $checked = in_array($key, $seleccionadas);

        if ($checked) {

            $activos = array_filter($activos, fn($i) => $i !== $key);

        } else {

            $activos[] = $key;

        }

        $url = build_filter_url(

            $base_url_parts[0],

            $base_url_parts[1],

            $base_url_parts[2],

            $base_url_parts[3],

            $activos

        );

        $out[] = [

            'label' => $label
                .' ('
                .($cuentas[$key] ?? 0)
                .')',

            'checked' => $checked,

            'url' => $url
        ];

    }

    return $out;

}

// ---- FILTRO: AMBINTES --
function generar_filtros_ambientes(array $ambientes_activa, array $base_url_parts): array {
    $opciones = ['1', '2', '3', '4'];
    $out = [];

    foreach ($opciones as $opcion) {
        $checked = in_array($opcion, $ambientes_activa, true);
        $nuevo_valor = $checked ? null : $opcion; // null lo elimina del query param

        $url = actualizar_query_param('ambientes', $nuevo_valor);

        $label = $opcion === '4' ? '4+ Ambientes' : $opcion . ' Ambientes';

        $out[] = [
            'label' => $label,
            'checked' => $checked,
            'url' => $url
        ];
    }

    return $out;
}

function generar_filtros_dormitorios(array $dormitorios_activa, array $base_url_parts, array $ambientes_activa) : array {
    $opciones = ['1', '2', '3', '4'];
    $out = [];

    foreach($opciones as $opcion) {
        $checked = in_array($opcion, $dormitorios_activa);
        $nuevo_valor = $checked ? [] : [$opcion];

        $url = build_filter_url(
            $base_url_parts[0],
            $base_url_parts[1],
            $base_url_parts[2],
            $base_url_parts[3],
            $base_url_parts[4],
            $ambientes_activa,
            $nuevo_valor
        );

        $label = $opcion === '4'
            ? '4+ Dormitorios'
            : $opcion.' Dormitorios';

        $out[] = [
            'label' => $label,
            'checked' => $checked,
            'url' => $url
        ];
    }

    return $out;
}