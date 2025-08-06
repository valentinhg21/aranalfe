<?php

/**

 * Template Name: Tasaciones

 */

?>

<?php get_header(); ?>

<main class="template-tasaciones">
    <?php if ( have_rows( 'portada' ) ) : ?>
            <?php while ( have_rows( 'portada' ) ) :
                    the_row(); ?>
                <?php 
                $imagen = get_sub_field( 'imagen' )['url'];
                $imagen_mobile = get_sub_field( 'imagen_mobile' )['url'] ?? $imagen;
                $titulo = get_sub_field( 'titulo' );
                $texto = get_sub_field( 'texto' );
                $cta = get_sub_field( 'cta' );
                ?>
                <style>
                    .portada{
                        background-image: url(<?php echo $imagen; ?>);
                    }
                    @media screen and (max-width: 878.5px) {
                        .portada{
                            background-image: url(<?php echo $imagen_mobile; ?>);
                        }
                    }
                </style>
                <section class="portada">
                    <div class="container">
                        <div class="content">
                            <?php 
                                insert_acf($titulo, 'h1');
                                echo $texto;
                                insert_button($cta, '', 'btn-stroke-white-to-white')
                            ?>
                        </div>
                    </div>
                </section>
            <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'banner' ) ) : ?>
        <?php while ( have_rows( 'banner' ) ) :
                the_row(); ?>
            <?php 
                $imagen = get_sub_field( 'imagen' )['url'] ?? '';
                $imagen_mobile = get_sub_field( 'imagen_mobile' )['url'] ?? $imagen;
                $contenido = get_sub_field( 'contenido' );
             ?>
            <section class="banner">
                <div class="container">
                    <div class="content" style="--bg-desktop: url(<?php echo esc_url( $imagen ); ?>);--bg-mobile: url(<?php echo esc_url( $imagen_mobile ); ?>);">
                        <?php echo $contenido; ?>
                    </div>
                </div>
            </section>
            <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'tarjetas' ) ) : ?>
        <?php while ( have_rows( 'tarjetas' ) ) :
                the_row(); ?>
            <section class="tarjetas">
                <div class="container">
                    <div class="title">
                        <?php $titulo = get_sub_field( 'titulo' ); ?>
                        <?php insert_acf($titulo, 'h2'); ?>
                    </div>
                    <div class="row">
                    <?php if ( have_rows( 'items' ) ) : ?>
                        <?php while ( have_rows( 'items' ) ) : the_row(); ?>
                      
                            <div class="col-xs-6 col-12">
                                <div class="tarjeta">
                                    <?php $imagen = get_sub_field( 'imagen' ); ?>
                                    <?php if($imagen): ?>
                                        <div class="icon">
                                            <?php insert_image($imagen); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="description">
                                        <?php $titulo = get_sub_field( 'titulo' ); ?>
                                        <?php insert_acf($titulo, 'h3'); ?>
                                        <?php echo get_sub_field( 'texto' ); ?>
                                    </div>
                                </div>
                            </div>
                        
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php $id = get_field( 'id' ); ?>
    <section class="seccion-1 scroll-margin"  id="<?php echo $id; ?>">
        <div class="container d-flex flex-row-md flex-column align-items-start justify-between">
            <div class="texto">
                <h2 class="title-form"><?php echo get_field('titulo'); ?></h2>
                <?php echo get_field('textos'); ?>
                <?php if (have_rows('conatcto')): ?>
                    <?php while (have_rows('conatcto')):
                        the_row(); ?>
                        

                        <div class="contacto">
                            <h2><?php echo get_sub_field('titulo'); ?></h2>
                            <?php if (have_rows('datos')): ?>
                                <?php while (have_rows('datos')):
                                    the_row(); ?>
                                    <div class="datos d-flex align-items-start">
                                        <div class="cont-img mr-1">
                                            <img src="<?php echo esc_url(get_sub_field('icono')['url']); ?>" class="img-contain" alt="">
                                        </div>
                                        <?php echo get_sub_field('dato'); ?>
                                    </div>
                                <?php endwhile; endif; ?>
                        </div>
                    <?php endwhile; endif; ?>
            </div>
            <div class="cont-form">
                <form id="singleForm" data-origen="Form Tasaciones" data-property="" data-tags="Tasacion Profesional" data-event="form_tasaciones">
                        <div class="input__container">
                            <label for="fullname">Nombre y Apellido</label>
                            <input type="text" id="fullname" name="fullname">
                        </div>
                        <div class="input__container">
                            <label for="telephone">Teléfono</label>
                            <input type="number" min="0" id="telephone" name="telephone">
                        </div>
                        <div class="input__container">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>  
                        <div class="input__container">
                            <label for="message">Mensaje</label>
                            <textarea name="message" id="message"></textarea>
                        </div>       
                        <div class="submit__input w-100">
                            <button type="submit" class="btn btn-red w-100" >Enviar</button>
                        </div> 
                        <div class="loading-form"><span class="loader"></span></div>         
                </form>
            </div>

        </div>
    </section>
</main>
<?php get_footer(); ?>