<?php 
add_action('init', function () {
    $base = '^propiedades';
    $query_base = 'index.php?pagename=propiedades';
    $params = []; // Si en el futuro agregás filtros por URL, ponelos acá

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

    // Regla para propiedad individual
    add_rewrite_rule(
        '^propiedad/([^/]+)/([0-9]+)/?$',
        'index.php?is_single_prop=1&slug=$matches[1]&propiedad_id=$matches[2]',
        'top'
    );

    // Regla para emprendimiento individual
    add_rewrite_rule(
        '^emprendimiento/([^/]+)/([0-9]+)/?$',
        'index.php?is_single_empre=1&slug=$matches[1]',
        'top'
    );
});

add_filter('query_vars', function ($vars) {
    return array_merge($vars, [
        'paged',
        'slug',
        'propiedad_id',
        'is_single_prop',
        'emprendimiento_id',
        'is_single_empre',
    ]);
});

add_action('template_redirect', function () {
    // Página individual propiedad
    if (get_query_var('is_single_prop')) {
        include get_template_directory() . '/single-propiedad.php';
        exit;
    }

    // Página individual emprendimiento
    if (get_query_var('is_single_empre')) {
        include get_template_directory() . '/single-emprendimiento.php';
        exit;
    }
});
