<?php

// add custom styles to the WordPress editor

function styles_custom_tinymce($settings) {
    $custom = array(
        array(
            'title' => 'Titulo H1',
            'block' => 'h1',
            'classes' => 'titulo-h1',
        ),
        array(
            'title' => 'Titulo H2',
            'block' => 'h2',
            'classes' => 'titulo-h2',
        ),
        array(
            'title' => 'Titulo H3',
            'block' => 'h3',
            'classes' => 'titulo-h3',
        ),
        array(
            'title' => 'Texto',
            'block' => 'p',
            'classes' => 'texto',
        )

    );

    $settings['style_formats'] = json_encode($custom);
    $settings['style_formats_disable'] = true;
    return $settings;
}

add_filter('tiny_mce_before_init', 'styles_custom_tinymce');

add_filter( 'should_load_separate_core_block_assets', '__return_true' );


function custom_tinymce_colors($init) {
    // Agrega tus colores personalizados a la paleta
    $custom_colors = '

        "E40729", "Red",
        "222222", "Black",

    ';
    
    // Agrega los colores personalizados a TinyMCE
    $init['textcolor_map'] = '[' . $custom_colors . ']';
    
    // Permitir que el selector de colores esté siempre disponible
    $init['textcolor_rows'] = 6; // Cambia este número si necesitas más filas

    return $init;
}
add_filter('tiny_mce_before_init', 'custom_tinymce_colors');