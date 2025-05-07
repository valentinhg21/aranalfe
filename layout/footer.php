<footer>
    <div class="hidden">
        <div class="container">
            <nav class="row">
                <div class="col-12">
                    <div class="logo" <?php animation('fade-in-right', 100);?>>
                        <?php display_custom_logo(); ?>
                    </div>

                </div>
                <div class="col-12 p-relative" <?php animation('fade-in-bottom', 500);?>>
                    <?php 
                        if (has_nav_menu('main-footer')) {
                            wp_nav_menu(array(
                                'theme_location' => 'main-footer',
                                'menu' => '',
                                'menu_class' => 'menu-left',
                                'menu_id' => '',
                                'container_class' => '',
                                'walker' => new Walker_Zetenta_Menu_Footer()
                            ));
                        }   
                    ?>

                </div>

                <div class="col-12" <?php animation('scale-in-center', 600);?>>
                    <div class="copy">
                        <?php $year = date("Y");?>
                        <p>
                            <a href="<?php echo get_home_url();?>" aria-label="Sitio. Todos los derechos reservados">©<?php echo $year;?>. Sitio. Todos los derechos reservados</a>
                        </p>
                        <p>
                            <a href="https://www.zetenta.com/web/es/" target="_blank" aria-label="Sitio Creado Diseñado y Desarrollador por Zetenta" class="creditos">Diseño y desarrollo web: Zetenta</a>
                        </p>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</footer>