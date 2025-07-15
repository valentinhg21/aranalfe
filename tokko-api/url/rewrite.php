<?php 
// Reescritura de URLs personalizadas
add_action('init', function () {
    $base = '^propiedades';
    $query_base = 'index.php?pagename=propiedades';
    $params = [];

    // ✅ Asegura que /propiedades/ funcione correctamente
    add_rewrite_rule(
        '^propiedades/?$',
        'index.php?pagename=propiedades',
        'top'
    );

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
        'index.php?is_single_empre=1&slug=$matches[1]&emprendimiento_id=$matches[2]',
        'top'
    );
});

// Registrar query vars personalizadas
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

// Template redirect + soporte para ?id=XXX en propiedad y emprendimiento
add_action('template_redirect', function () {
    // Acceso directo con ?id a propiedad
    if (isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/propiedad/') !== false) {
        set_query_var('propiedad_id', intval($_GET['id']));
        include get_template_directory() . '/single-propiedad.php';
        exit;
    }

    // Acceso directo con ?id a emprendimiento
    if (isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/emprendimiento/') !== false) {
        set_query_var('emprendimiento_id', intval($_GET['id']));
        include get_template_directory() . '/single-emprendimiento.php';
        exit;
    }

    // Página individual propiedad por URL amigable
    if (get_query_var('is_single_prop')) {
        include get_template_directory() . '/single-propiedad.php';
        exit;
    }

    // Página individual emprendimiento por URL amigable
    if (get_query_var('is_single_empre')) {
        include get_template_directory() . '/single-emprendimiento.php';
        exit;
    }
});
