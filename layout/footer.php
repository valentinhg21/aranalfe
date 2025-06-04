<footer>
    <div class="hidden">
        <div class="newsletter">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-sm-6 col-12">
                        <div class="content">
                            <h2>Accedé antes. Invertí mejor. Suscribite a nuestro newsletter.</h2>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-12">
                        <form action="">
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
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <nav class="row">
                <div class="col-sm-4 col-12" style="order:0;">
                    <div class="logo" <?php animation('fade-in-right', 100);?>>
                        <a href="<?php echo get_home_url()?>" title="Logo Aranalfe">
                            <?php render_svg(IMAGE . '/logo.svg') ?>
                        </a>

                    </div>

                </div>
                <div class="col-sm-5 col-12 p-relative col-links-menu" <?php animation('fade-in-bottom', 500);?>>
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
                <div class="col-sm-3 col-12 col-social" <?php animation('fade-in-bottom', 600);?>>
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
                        <?php if ( $linkedin = get_field( 'tiktok', 'options' ) ) : ?>
                        <li>
                            <a href="<?php echo esc_url( $tiktok );?>" target="_blank" title="tiktok">
                                <i class="fa-brands fa-tiktok"></i>
                            </a>
                        </li>
                        <?php endif; ?>

                    </ul>
                </div>
                <div class="col-12" <?php animation('scale-in-center', 700);?>>
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