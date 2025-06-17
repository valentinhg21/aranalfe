

<header id="main-header">
    <nav class="container navbar-header">
        <div class="logo">
            <?php #display_custom_logo(); ?>
            <a href="<?php echo get_home_url()?>" title="Logo Aranalfe">
                <?php render_svg(IMAGE_RELATIVE . '/logo.svg') ?>
            </a>  
        </div>
        <div class="menu-links-container"> 
            <div class="backdrop">

                <?php 
                    if (has_nav_menu('main-menu')) {
                        wp_nav_menu(array(
                            'theme_location' => 'main-menu',
                            'menu' => '',
                            'menu_class' => 'menu',
                            'menu_id' => 'menu-menu',
                            'container_class' => 'menu__container',
                            'walker' => new Walker_Zetenta_Menu()
                     
                        ));
                    }
                ?>
            </div>
        </div>
        <div class="actions-mobile d-none-md">
                <div class="hamburger hamburger--slider">
                    <div class="hamburger-box">
                    <div class="hamburger-inner"></div>
                    </div>
                </div>
        </div>

    </nav>
</header>