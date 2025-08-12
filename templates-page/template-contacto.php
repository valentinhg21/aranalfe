<?php 
/**

 * Template Name: Contacto

 */
?>

<?php get_header(); ?>
<main class="template-tasaciones template-contacto">
    <section class="seccion-1">
        <div class="container d-flex flex-row-md flex-column align-items-start justify-between">
            <div class="texto">
                <h1><?php echo get_field('titulo'); ?></h1>
                <p><?php echo get_field('texto') ?></p>
                <p><?php echo get_field('texto_2') ?></p>
                <?php if (have_rows('conatcto')): ?>
                    <?php while (have_rows('conatcto')):
                        the_row(); ?>
                        <div class="contacto d-block-md d-none">
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
                <form id="singleForm" data-origen="Form Contacto" data-property="" data-tags="Contacto" data-event="form_contacto">
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

             <?php if (have_rows('conatcto')): ?>
                    <?php while (have_rows('conatcto')):
                        the_row(); ?>
                        <div class="contacto d-block d-none-md">
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
    </section>
</main>
<?php get_footer();?>