<footer>
    <div class="hidden">
        <div class="newsletter">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-sm-6 col-12">
                        <div class="content">
                            <h2>Accedé antes. Invertí mejor. <br>Suscribite a nuestro newsletter.</h2>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <form action="" id="newsletterForm" data-origen="Newsletter" class="p-relative" data-event="form_newsletter">
                            <div class="input-newsletter">
                                <input type="text" name="email-newsletter" id="email-newsletter"
                                    placeholder="Ingresá tu email">
                                <button type="submit" class="btn-newsletter">Suscribirme</button>
                            </div>
                            <div class="checkbox">

                                <label for="whatsapp-newsletter">
                                    <input type="checkbox" name="whatsapp-newsletter" id="whatsapp-newsletter">
                                    <div class="checkmark"></div>
                                    Prefiero que me contacten via Whatsapp
                                </label>
                            </div>
                            <div class="loading-form"><span class="loader"></span></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <nav class="row">
                <div class="col-sm-4 col-12" style="order:0;">
                    <div class="logo">
                        <a href="<?php echo get_home_url()?>" title="Logo Aranalfe" class="flex-column">
                            <?php render_svg(IMAGE_RELATIVE . '/logo.svg') ?>
                            <p>MATRICULA: CUCICBA 2906</p>
                        </a>
                        
                    </div>

                </div>
                <div class="col-sm-5 col-12 p-relative col-links-menu">
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
                <div class="col-sm-3 col-12 col-social">
                    <ul class="social">
                        <?php if ( $facebook = get_field( 'facebook', 'options' ) ) : ?>
                        <li>
                            <a href="<?php echo esc_url( $facebook );?>" target="_blank" title="Facebook">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ( $instagram = get_field( 'instagram', 'options' ) ) : ?>
                        <li>
                            <a href="<?php echo esc_url( $instagram );?>" target="_blank" title="instagram">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if ( $linkedin = get_field( 'linkedin', 'options' ) ) : ?>
                        <li>
                            <a href="<?php echo esc_url( $linkedin );?>" target="_blank" title="linkedin">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                        </li>
                        <?php endif; ?>
             
                         
                        <?php $tiktok = get_field( 'tiktok', 'options' ); ?>
                        <?php if ( $tiktok ) : ?>
                            <li>
                                <a href="<?php echo esc_url( $tiktok );?>" target="_blank" title="tiktok">
                                    <i class="fa-brands fa-tiktok"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </div>
                <div class="col-12">
                    <div class="copy">
                        <?php $year = date("Y");?>
                        <p>
                            <a href="<?php echo get_home_url();?>"
                                aria-label="Aranalfe. Todos los derechos reservados">©<?php echo $year;?>. Aranalfe.
                                Todos los derechos reservados</a>
                        </p>
                        <p>
                            <a href="https://www.zetenta.com/web/es/" target="_blank"
                                aria-label="Sitio Creado Diseñado y Desarrollador por Zetenta" class="creditos">Diseño y
                                desarrollo web: Zetenta</a>
                        </p>
                    </div>
                </div>
            </nav>
        </div>
    </div>
</footer>
<?php if ( $numero_whatsapp = get_field( 'numero_whatsapp', 'options' ) ) : ?>
    <?php 
    $mensaje_general = get_field( 'mensaje_general', 'options' );
    $mensaje_para_propiedad = get_field( 'mensaje_para_propiedad', 'options' );
    $mensaje_para_emprendimientos = get_field( 'mensaje_para_emprendimientos', 'options' );

    // Armamos URL actual manualmente
    if ( get_query_var('is_single_prop') ) {
        $url_actual = home_url('/propiedad/' . get_query_var('slug') . '/' . get_query_var('propiedad_id') . '/');
        $mensaje_mostrar = $mensaje_para_propiedad . "\n\n" . $url_actual;
    } elseif ( is_singular('emprendimiento') ) {
        $url_actual = home_url('/emprendimiento/' . get_query_var('slug') . '/');
        $mensaje_mostrar = $mensaje_para_emprendimientos . "\n\n " . get_permalink();
    } else {
        $mensaje_mostrar = $mensaje_general;
    }

    $mensaje_encoded = urlencode($mensaje_mostrar);
    $link_whatsapp = "https://wa.me/$numero_whatsapp?text=$mensaje_encoded";
    ?>

    <a href="<?= esc_url($link_whatsapp); ?>" target="_blank" title="Enviar mensaje por WhatsApp" class="whatsapp-btn">
        <i class="fa-brands fa-whatsapp"></i>

    </a>
<?php endif; ?>
