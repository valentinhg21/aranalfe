<?php 


add_action('acf/init', function() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title'    => 'Ajuste General',
            'menu_title'    => 'Tema',
            'menu_slug'     => 'general',
            'capability'    => 'edit_posts',
            'redirect'      => false,
            'position'      => 80
        ));
    }
});




?>