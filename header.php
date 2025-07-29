
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php if (get_query_var('is_single_prop') ): ?>
    <meta name="description" content="<?php echo $args['description']?>">
    <meta property="og:title" content="<?php echo $args['title']?>" />
    <meta property="og:description" content="<?php echo $args['description']?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?php echo $args['permalink']?>" />
    <meta property="og:image" content="<?php echo $args['image']?>" />
    <meta property="og:site_name" content="Aranalfe" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $args['title']?>" />
    <meta name="twitter:description" content="<?php echo $args['description']?>" />
    <meta name="twitter:image" content="<?php echo $args['image']?>" />
  <?php endif; ?>

  
  <!-- Google Tag Manager -->
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-M3HTMKQ7');</script>
  <!-- End Google Tag Manager -->
  <?php wp_head(); ?>
    <?php if (!get_query_var('is_single_prop') ): ?>
      <title>
        <?php
          if (function_exists('wpseo_title')) {
            echo wpseo_title(); // Usa el título de Yoast si está activo.
          } elseif (is_front_page() || is_home()) {
            echo get_bloginfo('name') . ' | ' . get_bloginfo('description');
          } else {
            wp_title('|', true, 'right');
          }
        ?>
      </title>
      <?php else: ?>
        <title><?php echo $args['title']?></title>
    <?php endif; ?>
   


</head>




<body class="theme">
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M3HTMKQ7"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <input type="hidden" name="title-page" value="<?php echo get_the_title(); ?>" id="title-page">

  <?php require_once('layout/header.php');?>
