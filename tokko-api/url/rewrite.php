<?php 



add_action('init', function () {
    $base = '^propiedades';
    $query_base = 'index.php?pagename=propiedades';
    // Lista de parámetros en orden esperada en la URL
    $params = [];
    // Reglas sin paginación
    for ($i = count($params); $i >= 3; $i--) {

        $pattern = $base;

        $query = $query_base;

        for ($j = 0; $j < $i; $j++) {

            $pattern .= '/([^/]+)';

            $query .= '&' . $params[$j] . '=$matches[' . ($j + 1) . ']';

        }

        $pattern .= '/?$';

        add_rewrite_rule($pattern, $query, 'top');

    }
    // Reglas con paginación

    for ($i = count($params); $i >= 3; $i--) {

        $pattern = $base;

        $query = $query_base;

        for ($j = 0; $j < $i; $j++) {

            $pattern .= '/([^/]+)';

            $query .= '&' . $params[$j] . '=$matches[' . ($j + 1) . ']';

        }

        $pattern .= '/page/([0-9]{1,})/?$';

        $query .= '&paged=$matches[' . ($i + 1) . ']';

        add_rewrite_rule($pattern, $query, 'top');

    }

    // Regla para propiedad individual: /propiedad/slug/id
    add_rewrite_rule(
        '^propiedad/([^/]+)/([0-9]+)/?$',
        'index.php?is_single_prop=1&slug=$matches[1]&propiedad_id=$matches[2]',
        'top'
    );
    // Regla para emprendimiento individual: /emprendimiento/slug/id
    add_rewrite_rule(
        '^emprendimiento/([^/]+)/([0-9]+)/?$',
        'index.php?is_single_empre=1&slug=$matches[1]&emprendimiento_id=$matches[2]',
        'top'
    );
});



add_filter('query_vars', function ($vars) {

    return array_merge($vars, [
        // 'operacion',
        // 'tipo',
        // 'ubicacion',
        // 'localidad',
        // 'antiguedad',
        'paged',
        'slug',
        'propiedad_id',
        'is_single_prop', // nueva var personalizada
    ]);

});


add_action('template_redirect', function () {
    if (get_query_var('is_single_prop')) {
        include get_template_directory() . '/single-propiedad.php';
        exit;
    }
});


// add_action('template_redirect', function () {
//     if (get_query_var('emprendimiento_id')) {
//         include get_template_directory() . '/single-emprendimiento.php';
//         exit;
//     }
// });