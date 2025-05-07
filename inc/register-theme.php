<?php


if ( ! isset( $content_width ) )

	$content_width = 1366;

if ( ! function_exists('zetenta_custom_theme_features') ) {

	function zetenta_custom_theme_features()  {
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-formats', array( 'status', 'gallery', 'image', 'video', 'audio' ) );
		add_theme_support( 'post-thumbnails');
		$background_args = array(
			'default-color'          => 'ffff',
			'default-image'          => '',
			'default-repeat'         => '',
			'default-position-x'     => '',
			'wp-head-callback'       => '_custom_background_cb',
			'admin-head-callback'    => '',
			'admin-preview-callback' => '',
		);
		add_theme_support( 'custom-background', $background_args );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
		load_theme_textdomain( 'zetenta', get_template_directory() . '/languages' );
	}
	add_action( 'after_setup_theme', 'zetenta_custom_theme_features' );
}


function logo_custom_setup(){
    add_theme_support( 'custom-logo', array(
		'height'      => 265,
		'width'       => 36,
        'flex-width'  => true,
        'flex-height' => true,
	) );
}


add_action( 'after_setup_theme', 'logo_custom_setup' );
function display_custom_logo() {
	if ( function_exists( 'the_custom_logo' ) ) {
		the_custom_logo();
	}
}

function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['ico'] = 'image/ico';
	return $mimes;
  }

add_filter('upload_mimes', 'cc_mime_types');

function custom_upload_mimes ( $existing_mimes=array() ) {
    $existing_mimes['dwg'] = 'image/vnd.dwg';
    return $existing_mimes;
}
add_filter('upload_mimes', 'custom_upload_mimes');



function add_type_attribute($tag, $handle, $src) {
    // if not your script, do nothing and return original $tag
    if ( 'zetenta-scripts' !== $handle) {
        return $tag;
    }
    // change the script tag by adding type="module" and return it.
    $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>';
    return $tag;
}

add_filter('script_loader_tag', 'add_type_attribute' , 10, 3);






function img_responsive($image_id,$image_size,$max_width){
	// check the image ID is not blank
	if($image_id != '') {
		// set the default src image size
		$image_src = wp_get_attachment_image_url( $image_id, $image_size );
		// set the srcset with various image sizes
		$image_srcset = wp_get_attachment_image_srcset( $image_id, $image_size );
		// generate the markup for the responsive image
		return 'src="'.$image_src.'" srcset="'.$image_srcset.'" sizes="(max-width: '.$max_width.') 100vw, '.$max_width.'"';
	}
}


add_action('init', function() {
	remove_theme_support('core-block-patterns');
});



// REMOVE BLOCKS DEFAULT
remove_filter( 'render_block', 'wp_render_layout_support_flag', 10, 2 );
remove_filter( 'render_block', 'gutenberg_render_layout_support_flag', 10, 2 );
add_action('init', function() {
	remove_theme_support('core-block-patterns');
});

function prefix_remove_core_block_styles() {
	wp_dequeue_style( 'wp-block-columns' );
	wp_dequeue_style( 'wp-block-column' );
}
add_action( 'wp_enqueue_scripts', 'prefix_remove_core_block_styles' );

function smartwp_remove_wp_block_library_css(){
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' ); // Remove WooCommerce block CSS
}


add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );
add_theme_support( 'disable-layout-styles' );

// FIXED IMAGES
add_filter(
	'wp_content_img_tag',
	static function ( $image ) {
			return str_replace( ' sizes="auto, ', ' sizes="', $image );
	}
);
add_filter(
	'wp_get_attachment_image_attributes',
	static function ( $attr ) {
			if ( isset( $attr['sizes'] ) ) {
					$attr['sizes'] = preg_replace( '/^auto, /', '', $attr['sizes'] );
			}
			return $attr;
	}
);















