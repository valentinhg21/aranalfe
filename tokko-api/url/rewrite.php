<?php 
// Reescritura de URLs personalizadas
add_action('init', function () {
    // Regla para propiedad individual
    add_rewrite_rule(
        '^propiedad/([^/]+)/([0-9]+)/?$',
        'index.php?is_single_prop=1&slug=$matches[1]&propiedad_id=$matches[2]',
        'top'
    );
});

// Registrar query vars personalizadas
add_filter('query_vars', function ($vars) {
    return array_merge($vars, [
        'slug',
        'propiedad_id',
        'is_single_prop',
        'id'
    ]);
});

// Template redirect + soporte para ?id=XXX y slug
add_action('template_redirect', function () {


    // /propiedad/slug/id
    if (isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/propiedad/') !== false) {
        set_query_var('propiedad_id', intval($_GET['id']));
        include get_template_directory() . '/single-propiedad.php';
        exit;
    }
        // Para emprendimiento con ?id=XXX
    if (is_page('emprendimiento') && get_query_var('id')) {
        $id = intval(get_query_var('id'));
        // setear var si usas slug también, o cargar data con $id
        // cargar plantilla detalle emprendimiento
        include get_template_directory() . '/single-emprendimiento.php';
        exit;
    }

    // Página individual propiedad por URL amigable
    if (get_query_var('is_single_prop')) {
        include get_template_directory() . '/single-propiedad.php';
        exit;
    }

});

// // --------------------------------------------------------------------------
// // Reescritura de URLs personalizadas
// add_action('init', function () {


//     // Regla para propiedad individual
//     add_rewrite_rule(
//         '^propiedad/([^/]+)/([0-9]+)/?$',
//         'index.php?is_single_prop=1&slug=$matches[1]&propiedad_id=$matches[2]',
//         'top'
//     );


// });

// // Registrar query vars personalizadas
// add_filter('query_vars', function ($vars) {
//     return array_merge($vars, [
//         'paged',
//         'slug',
//         'propiedad_id',
//         'is_single_prop',
//         'id',

//     ]);
// });

// // Template redirect + soporte para ?id=XXX en propiedad y emprendimiento
// add_action('template_redirect', function () {
//     // Acceso directo con ?id a propiedad
//     if (isset($_GET['id']) && strpos($_SERVER['REQUEST_URI'], '/propiedad/') !== false) {
//         set_query_var('propiedad_id', intval($_GET['id']));
//         include get_template_directory() . '/single-propiedad.php';
//         exit;
//     }

//     // Página individual propiedad por URL amigable
//     if (get_query_var('is_single_prop')) {
//         include get_template_directory() . '/single-propiedad.php';
//         exit;
//     }

//     // Para emprendimiento con ?id=XXX
//     if (is_page('emprendimiento') && get_query_var('id')) {
//         $id = intval(get_query_var('id'));
//         include get_template_directory() . '/single-emprendimiento.php';
//         exit;
//     }




// });
