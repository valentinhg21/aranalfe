<?php 



/**



 * Template Name: Nosotros



 */



?>



<?php get_header(); ?>

<main class="template-nosotros">

    <?php if ( have_rows( 'seccion_1' ) ) : ?>

    <?php while ( have_rows( 'seccion_1' ) ) :

        the_row(); ?>

        <section class="section-1 d-flex justfy-center">

            <div class="container d-flex flex-row-md flex-column align-items-center justify-between">

                <div class="cont-texto">

                    <h1><?php echo get_sub_field('titulo')?></h1>

                    <div class="texto"><?php echo get_sub_field('texto')?></div>

                </div>

                <div class="cont-img">

                    <img src="<?php echo esc_url(get_sub_field('imagen')['url'])?>" class="img-cover" alt="">

                </div>

            </div>

        </section>

    <?php endwhile; endif;?>

        <?php if ( have_rows( 'seccion_2' ) ) : ?>

    <?php while ( have_rows( 'seccion_2' ) ) :

        the_row(); ?>

    <section class="section-2">

        <div class="container">

            <div class="grid-4">

                <?php if ( have_rows( 'tarjeta' ) ) : ?>

                <?php while ( have_rows( 'tarjeta' ) ) :

                    the_row(); ?>

                <div class="tarjeta d-flex flex-column-md flex-row align-items-start">

                    <div class="cont-img">

                        <img src="<?php echo esc_url(get_sub_field('icono')['url'])?>" class="img-contain" alt="">

                    </div>

                    <div class="cont-texto">

                        <h3><?php echo get_sub_field('titulo');?></h3>

                        <p><?php echo get_sub_field('texto');?></p>

                    </div>

                </div>

                <?php endwhile; endif;?>

            </div>

        </div>

    </section>

    <?php endwhile; endif;?>

    <?php if ( have_rows( 'seccion_3' ) ) : ?>

    <?php while ( have_rows( 'seccion_3' ) ) :

    the_row(); ?>

    <section class="section-3">

        <div class="container d-flex flex-row-md flex-column align-items-center justfy-center">
            <div class="row">
                <div class="col-sm-6 col-12">
                    <div class="cont-img">

                        <img src="<?php echo esc_url(get_sub_field('imagen')['url'])?>" class="img-cover" alt="">

                    </div>
                </div>
                <div class="col-sm-6 col-12">
                    <div class="cont-texto d-flex flex-column ">

                        <h2><?php echo get_sub_field('titulo')?></h2>

                        <div class="texto"><?php echo get_sub_field('texto')?></div>

                    </div>
                </div>
            </div>




        </div>

    </section>

    <?php endwhile; endif;?>

    <?php if ( have_rows( 'seccion_4' ) ) : ?>

    <?php while ( have_rows( 'seccion_4' ) ) :

    the_row(); ?>

     <section class="section-4">

        <div class="container text-center-md text-start">

            <h2>

                <?php echo get_sub_field('texto');?>

            </h2>

        </div>

     </section>

     <?php endwhile; endif;?>



    <?php if ( have_rows( 'seccion_5' ) ) : ?>

    <?php while ( have_rows( 'seccion_5' ) ) :

    the_row(); ?>

    <section class="section-5">

        <div class="container d-flex flex-column">

            <h2 class="text-center"><?php echo get_sub_field('titulo');?></h2>

            <div class="d-flex flex-row-md align-items-center justify-between flex-wrap cont-persona">

                  <?php if ( have_rows( 'equipo' ) ) : ?>

                    <?php while ( have_rows( 'equipo' ) ) :

                    the_row(); ?> 

                    <div class="persona">

                        <h3><?php echo get_sub_field('nombre');?></h3>

                        <p><?php echo get_sub_field('puesto');?></p>

                    </div> 

                    <div class="hr"></div>

                    <?php endwhile; endif;?>

            </div>

        </div>

    </section>

    <?php endwhile; endif;?>

</main>

<?php get_footer();?>