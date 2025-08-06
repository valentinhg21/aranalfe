<?php 



function libraries(){
    wp_register_script( 'validator', LIB . '/validator/validator.js', array(), '1.0', true);
    wp_register_script( 'splide-js', LIB . '/splide/splide.min.js', array(), '4.1.3', true);
    wp_register_script( 'helper-js', LIB . '/helper/helper.js', array(), '1.0', true);
    wp_register_script( 'tokko-js', LIB . '/tokko/helper.js', array(), '1.0', true);
    wp_register_script( 'selects-js', LIB . '/selects/selects.js', array(), '1.0', true);
    wp_register_script( 'countUp-js', LIB . '/countUp/countUp.min.js', array(), '1.0', true);
    wp_register_script( 'sweetalert2-js', LIB . '/sweetalert2/sweetalert2.min.js', array(), '11.16.1', true);
    wp_register_script( 'leaflet', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js', array(), '1.9.4', true);
    wp_register_script( 'fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', array(), '1.0', true);
    wp_register_script( 'swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11', true);
    wp_register_script( 'tom-select-js', LIB . '/tom-select/tom-select.complete.min.js', array(), '2.4', true);

    wp_register_style('splide-css', LIB . '/splide/splide.min.css', array(), '1.9.4', 'all');
    wp_register_style('sweetalert2-css', LIB . '/sweetalert2/sweetalert2.min.css', array(), '11.16.1', 'all');
    wp_register_style('fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', array(), '1.0', 'all');
    wp_register_style('leaflet-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css', array('fontawesome'), '1.4.1', 'all');
    wp_register_style('splide-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11', 'all');
    wp_register_style('tom-select-css', LIB . '/tom-select/tom-select.css', array(), '2.4', 'all');
}

add_action('wp_enqueue_scripts', 'libraries');





function zetenta_theme_styles() {
    $url_fontawesome = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css';
    wp_register_style('fontawesome', $url_fontawesome, [], '6.4.2', 'all');

    $css_file = get_stylesheet_directory() . '/dist/css/theme.min.css';
    $css_uri  = get_stylesheet_directory_uri() . '/dist/css/theme.min.css';
    $version  = file_exists($css_file) ? filemtime($css_file) : time();

    wp_register_style(
        'zetenta-styles',
        $css_uri,
        ['fontawesome', 'sweetalert2-css', 'leaflet-css', 'splide-css', 'fancybox-css', 'tom-select-css'],
        $version,
        'all'
    );
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
    wp_register_script( 'zetenta-scripts', JS . '/index.min.js', array('sm-3-scripts','selects-js', 'leaflet', 'tokko-js', 'validator', 'sweetalert2-js', 'splide-js', 'fancybox-js', 'swiper-js', 'tom-select-js'), '1.0', true);
    wp_enqueue_script('zetenta-scripts');
    wp_localize_script( 'sm-scripts', 'ajax_var', array(
        'url'    => admin_url( 'admin-ajax.php' ),
        'theme'    => ROOT,
        'lib_url'    => LIB,
        'site' => home_url('/wp-json/', 'https'),
        'image' => IMAGE,
        'ajax' =>  get_site_url() . '/ajax/'
    ) );
}

add_action('wp_enqueue_scripts', 'zetenta_theme_scripts');



function zetenta_disable_browser_cache() {
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
}
add_action('send_headers', 'zetenta_disable_browser_cache');





