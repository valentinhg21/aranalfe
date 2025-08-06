<?php 


// Configurar ACF para guardar los archivos JSON
add_filter('acf/settings/save_json', function() {
    return get_template_directory() . '/acf-json';
});

// Configurar ACF para cargar los archivos JSON
add_filter('acf/settings/load_json', function($paths) {
    // Eliminar la ruta por defecto
    unset($paths[0]);
    // Agregar la nueva ruta
    $paths[] = get_template_directory() . '/acf-json';
    return $paths;
});



