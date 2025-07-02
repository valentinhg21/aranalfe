<?php 
define('WPS_CUSTOM_LOGIN_SLUG', 'login');

// Bloquear acceso directo a wp-login.php si no está logueado
add_action('init', function () {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false && !is_user_logged_in()) {
        wp_die('No autorizado', 'Error 403', ['response' => 403]);
    }
}, 1);

// Reemplazar URLs generadas con la nueva ruta
add_filter('site_url', function ($url, $path, $scheme, $blog_id) {
    if ($path === 'wp-login.php' || strpos($path, 'wp-login.php') !== false) {
        return str_replace('wp-login.php', WPS_CUSTOM_LOGIN_SLUG, $url);
    }
    return $url;
}, 10, 4);

// Interceptar la nueva URL y cargar wp-login.php
add_action('parse_request', function ($wp) {
    if (trim($wp->request, '/') === WPS_CUSTOM_LOGIN_SLUG) {
        global $user_login, $error;
        $user_login = isset($_POST['log']) ? sanitize_user($_POST['log']) : '';
        $error = ''; // <- evitar el warning
        require ABSPATH . 'wp-login.php';
        exit;
    }
});

// Login CSS
// Reemplazar el logo por uno personalizado
function custom_login_logo() {
    ?>
    <style type="text/css">
        body.login {
            background-color:#e40729; /* Fondo claro */
        }
        .login h1 a {
            background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo.svg') !important;
            background-size: contain !important;
            width: 100% !important;
            height: 100px !important;
        }
        .login form {
            background: #fff !important;
            border-radius: 10px !important;
            box-shadow: none!important;
        }
        .login #login_error, .login .message {
            border-left: 4px solid #007cba!important;
        }
        .language-switcher form{
            background: transparent !important;
        }
        .login #backtoblog a, .login #nav a{
            color: #fff !important;
        }
        .login #backtoblog a, .login #nav a:hover{
            color: #fff !important;
        }
        .language-switcher label .dashicons{
            color: #fff !important;
        }
        #wp-submit{
            background: #e40729 !important;
            border-color: #e40729 !important;
        }
        #wp-submit:hover{
             background: #fff !important;
             color: #e40729 !important;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'custom_login_logo' );

// Cambiar la URL del logo (por defecto va a wordpress.org)
function custom_login_logo_url() {
    return home_url(); // Redirige al home del sitio
}
add_filter( 'login_headerurl', 'custom_login_logo_url' );

// Cambiar el texto del logo
function custom_login_logo_url_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'custom_login_logo_url_title' );