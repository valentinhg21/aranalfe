<?php 

function zetenta_menus(){
    register_nav_menus(array(
        'main-menu' => __('Header', 'zetenta'),
        'main-footer' => __('Footer', 'zetenta'),
      
    ));
}

add_action('init', 'zetenta_menus');

