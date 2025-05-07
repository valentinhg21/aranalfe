<?php
function register_acf_blocks() {
    if ( function_exists( 'acf_register_block_type' ) ) {
        $blocks_dir = get_template_directory() . '/blocks';

        $blocks = glob($blocks_dir . '/*', GLOB_ONLYDIR);
        foreach ( $blocks as $block ) {
            $block_name = basename($block);

            register_block_type( $block . '/block.json');
        }


    }
}
add_action( 'acf/init', 'register_acf_blocks' );

function allow_acf_blocks() {
    $permitir_bloques = array();
    $blocks_dir = get_template_directory() . '/blocks';
    $blocks = glob($blocks_dir . '/*', GLOB_ONLYDIR);

    foreach ($blocks as $block) {
        $block_name = basename($block);
        $permitir_bloques[] = 'acf/' . $block_name;
    }

    return $permitir_bloques;
}

add_filter('allowed_block_types_all', 'allow_acf_blocks');

function add_custom_block_categories( $categories, $post ) {
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'general',
                'title' => __('General', 'General'),
            )
        )
    );
}
add_filter( 'block_categories_all', 'add_custom_block_categories', 10, 2 );

