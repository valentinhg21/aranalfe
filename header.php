
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="google-site-verification" content="9MNDb5ioK748C9XGvVU-455QhFGEtXe-Xkx-RdDIb4k" />
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
  <?php wp_head(); ?>

</head>





<body class="theme">
  <input type="hidden" name="title-page" value="<?php echo get_the_title(); ?>" id="title-page">

  <?php require_once('layout/header.php');?>
