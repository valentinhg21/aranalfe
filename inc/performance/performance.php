<?php
// Asegurarse de no acceder directamente al archivo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Salir si se accede directamente
}

/**
 * Optimizaciones para el backend de WordPress y WooCommerce
 */
class Backend_Optimization {

    public function __construct() {
        add_action( 'init', array( $this, 'disable_wp_embeds' ) );
        add_action( 'init', array( $this, 'disable_wp_emojicons' ) );
        add_action( 'admin_init', array( $this, 'disable_comments_and_trackbacks' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'disable_dashicons_for_non_admins' ) );
        add_action( 'init', array( $this, 'limit_post_revisions' ) );
        add_action( 'init', array( $this, 'increase_php_memory' ) );
        add_action( 'init', array( $this, 'disable_heartbeat' ) );

        add_action( 'wp_scheduled_delete', array( $this, 'clean_up_revisions' ) );
        add_action( 'wp_scheduled_delete', array( $this, 'clean_up_transients' ) );
        add_action( 'wp_scheduled_delete', array( $this, 'optimize_database' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'disable_jquery_in_admin' ) );

        add_action( 'do_feed', array( $this, 'disable_feeds' ), 1 );
        add_action( 'do_feed_rdf', array( $this, 'disable_feeds' ), 1 );
        add_action( 'do_feed_rss', array( $this, 'disable_feeds' ), 1 );
        add_action( 'do_feed_rss2', array( $this, 'disable_feeds' ), 1 );
        add_action( 'do_feed_atom', array( $this, 'disable_feeds' ), 1 );

        add_action( 'admin_init', array( $this, 'optimize_woocommerce_admin' ) );
    }

    public function disable_wp_embeds() {
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
        add_filter( 'embed_oembed_discover', '__return_false' );
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    }

    public function disable_wp_emojicons() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    }

    public function limit_post_revisions() {
        if ( ! defined( 'WP_POST_REVISIONS' ) ) {
            define( 'WP_POST_REVISIONS', 3 );
        }
    }

    public function increase_php_memory() {
        if ( ! defined( 'WP_MEMORY_LIMIT' ) ) {
            define( 'WP_MEMORY_LIMIT', '256M' );
        }
        if ( ! defined( 'WP_MAX_MEMORY_LIMIT' ) ) {
            define( 'WP_MAX_MEMORY_LIMIT', '512M' );
        }
    }

    public function disable_heartbeat() {
        wp_deregister_script( 'heartbeat' );
    }

    public function disable_comments_and_trackbacks() {
        add_filter( 'comments_open', '__return_false', 20, 2 );
        add_filter( 'pings_open', '__return_false', 20, 2 );

        add_action( 'pre_get_posts', function( $query ) {
            if ( is_admin() ) {
                $query->set( 'comments', false );
            }
        });
    }

    public function disable_dashicons_for_non_admins() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_deregister_style( 'dashicons' );
        }
    }

    public function clean_up_revisions() {
        global $wpdb;
        $wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = 'revision' AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY)" );
    }

    public function clean_up_transients() {
        global $wpdb;
        $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_%' AND autoload = 'no'" );
    }

    public function optimize_database() {
        global $wpdb;
        $tables = array(
            $wpdb->posts,
            $wpdb->postmeta,
            $wpdb->comments,
            $wpdb->commentmeta,
            $wpdb->options,
            $wpdb->usermeta,
            $wpdb->terms,
            $wpdb->termmeta,
            $wpdb->term_relationships,
            $wpdb->term_taxonomy,
            $wpdb->woocommerce_order_items,
            $wpdb->woocommerce_order_itemmeta,
            $wpdb->woocommerce_sessions,
        );

        foreach ( $tables as $table ) {
            $wpdb->query( "OPTIMIZE TABLE $table" );
        }
    }

    public function disable_jquery_in_admin() {
        // wp_deregister_script( 'jquery' );
    }

    public function disable_feeds() {
        wp_die( __( 'No feed disponible, por favor visita nuestra página principal.' ), '', array( 'response' => 403 ) );
    }

    public function optimize_woocommerce_admin() {
        if ( is_admin() && isset( $_GET['post_type'] ) && $_GET['post_type'] === 'product' ) {
            // Optimizaciones específicas
        }
    }
}

new Backend_Optimization();

if ( ! wp_next_scheduled( 'wp_scheduled_delete' ) ) {
    wp_schedule_event( time(), 'daily', 'wp_scheduled_delete' );
}

add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'woocommerce_admin_meta_boxes_product_data', '__return_empty_array' );

remove_action( 'woocommerce_new_product', 'wp_save_post_revision' );
remove_action( 'woocommerce_update_product', 'wp_save_post_revision' );

add_filter( 'woocommerce_product_data_revision_limit', function() {
    return 3;
} );

function dequeue_unnecessary_admin_scripts() {
    if ( is_admin() ) {
        // Ejemplo de desactivación de scripts innecesarios
    }
}
add_action( 'admin_enqueue_scripts', 'dequeue_unnecessary_admin_scripts', 100 );

function optimize_woocommerce_product_queries( $q ) {
    if ( ! is_admin() && is_post_type_archive( 'product' ) && $q->is_main_query() ) {
        $q->set( 'posts_per_page', 20 );
    }
}
add_action( 'pre_get_posts', 'optimize_woocommerce_product_queries' );

function reduce_autosave_interval() {
    return 300;
}
add_filter( 'autosave_interval', 'reduce_autosave_interval' );

function limit_product_revisions( $revisions, $post ) {
    if ( 'product' === get_post_type( $post ) ) {
        return 3;
    }
    return $revisions;
}
add_filter( 'wp_revisions_to_keep', 'limit_product_revisions', 10, 2 );

function remove_dashboard_widgets() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
}
add_action( 'wp_dashboard_setup', 'remove_dashboard_widgets' );

function optimize_wc_product_editor() {
    // add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );
}
add_action( 'init', 'optimize_wc_product_editor' );

add_action( 'admin_menu', function() {
    remove_menu_page( 'edit-comments.php' );
});