<?php 

function libraries(){
    wp_register_script( 'validator', LIB . '/validator/validator.js', array(), '1.0', true);
    wp_register_script( 'splide-js', LIB . '/splide/splide.min.js', array(), '4.1.3', true);
    wp_register_script( 'helper-js', LIB . '/helper/helper.js', array(), '1.0', true);
    wp_register_style('splide-css', LIB . '/splide/splide.min.css', array(), '1.9.4', 'all');
}
add_action('wp_enqueue_scripts', 'libraries');


function zetenta_theme_styles(){
    $url_fontawesome = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css';
    wp_register_style('fontawesome', $url_fontawesome, array(), '6.4.2', 'all');
    wp_register_style('zetenta-styles', get_stylesheet_uri(), array('fontawesome'), '1.0', 'all');
    wp_enqueue_style('zetenta-styles');
}
add_action('wp_enqueue_scripts', 'zetenta_theme_styles');
function zetenta_theme_scripts(){
    $SM = "https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.8/ScrollMagic.min.js";
    $SM2 = "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js";
    $SM3 = "https://cdnjs.cloudflare.com/ajax/libs/ScrollMagic/2.0.8/plugins/animation.gsap.min.js";
    wp_register_script( 'sm-scripts', $SM, array('jquery'), '2.0.8', true);
    wp_register_script( 'sm-2-scripts', $SM2, array('sm-scripts'), '3.9.1', true);
    wp_register_script( 'sm-3-scripts', $SM3, array('sm-2-scripts', 'splide-js'), '2.0.8', true);
    wp_register_script( 'zetenta-scripts', JS . '/index.min.js', array('sm-3-scripts'), '1.0', true);
    wp_enqueue_script('zetenta-scripts');
    wp_localize_script( 'zetenta-scripts', 'ajax_var', array(
        'url'    => admin_url( 'admin-ajax.php' ),
        'theme'    => ROOT,
        'site' => home_url('/wp-json/', 'https'),
        
    ) );

}
add_action('wp_enqueue_scripts', 'zetenta_theme_scripts');



