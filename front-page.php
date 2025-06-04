<?php get_header(); ?>
<?php 
    $portada_img = get_the_post_thumbnail_url();
    $developments_under_construccion = filter_developments([], ['construction_status' => 4], 3);
    
?>

<main class="front-page">
    <?php if ( have_rows( 'portada' ) ) : ?>
        <?php while ( have_rows( 'portada' ) ) :
        the_row(); ?>
            <?php
                $imagen_mobile = get_sub_field( 'imagen' )['url'] ?? $portada_img; 
            ?> 

                <section class="hero" 
                data-image-desktop="<?php echo esc_url( $portada_img ); ?>" 
                data-image-mobile="<?php echo esc_url( $imagen_mobile ); ?>" 
                style="--background-image-desktop: url(<?php echo esc_url( $portada_img ); ?>);
                --background-image-mobile: url(<?php echo esc_url( $imagen_mobile ); ?>);"
                >
                    <div class="container">
                        <div class="content">
                            <?php  $titulo = get_sub_field('titulo'); ?>
                            <?php insert_acf($titulo, 'h1'); ?>
                            <?php get_template_part('/template-parts/content', 'search');?>
                        </div>
                    </div>
                </section>
            <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'emprendimientos' ) ) : ?>
        <?php while ( have_rows( 'emprendimientos' ) ) :
        the_row(); ?>
            <section class="developments-feature">
                <div class="container">
                    <?php  $titulo = get_sub_field('titulo'); ?>
                    <div class="header-title">
                        <?php insert_acf($titulo, 'h2'); ?>
                        <div class="button__container d-flex-md d-none">
                            <a href="#" class="btn btn-red-outline" title="Ver mas">
                                Ver todos
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($developments_under_construccion ?? [] as $item): ?>
                            <?php 
                                $name = $item['name'];
                                $image = $item['photos'][0]['image'];
                                $location = get_full_location($item['location']['full_location']);
                                $status = get_construction_status($item['construction_status']);
                            
                            ?>
                            <div class="col-sm-4 col-xs-6 col-12">
                                <a href="#" class="card-feature" title="Aguilar Point"> 
                                    <div class="image">
                                        <img src="<?php echo $image;?>" alt="<?php echo $name;?>" width="100%" height="100%" loading="lazy">
                                    </div>
                                    <div class="content">
                                        <span class="status"><?php echo  $status;?></span>
                                        <h3 class="title"><?php echo  $name;?></h3>
                                        <p class="location"><?php echo  $location;?></p>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="button__container d-none-md d-flex justify-center align-items-center mt-4">
                        <a href="#" class="btn btn-red-outline" title="Ver mas">
                            Ver todos
                        </a>
                    </div>
                </div>
            </section>
            <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'propiedades' ) ) : ?>
        <?php while ( have_rows( 'propiedades' ) ) :
        the_row(); ?>
            <section class="property">
                <div class="container">
                    <?php  $titulo = get_sub_field('titulo'); ?>
                    <div class="header-title">
                        <?php insert_acf($titulo, 'h2'); ?>
                        <div class="button__container d-flex-md d-none">
                            <a href="#" class="btn btn-red-outline" title="Ver mas">
                                Ver todos
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <?php if ( have_rows( 'categorias' ) ) : ?>
                            <?php while ( have_rows( 'categorias' ) ) :
                            the_row(); ?>
                                <?php 
                                   $title = get_sub_field( 'titulo' );
                                    $image = get_sub_field('imagen');
                                ?>
                                <div class="col-sm-4 col-6">
                                    <a href="#" class="card-category" title="Aguilar Point"> 
                                        <div class="image">
                                            <?php insert_image($image); ?>
                                        </div>
                                        <div class="content">
                                            <?php insert_acf($title, 'h3', 'title') ?>
                                        </div>
                                    </a>
                                </div>
                              
                            <?php endwhile; ?>
                        <?php endif; ?>

                    </div>
                </div>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'banner' ) ) : ?>
        <?php while ( have_rows( 'banner' ) ) :
        the_row(); ?>
             <?php 
                $titulo = get_sub_field( 'titulo' ); 
                $image_banner = get_sub_field('image')['url'] ?? '';
             ?>            
             <section class="banner" style="background-image:url(<?php echo esc_url($image_banner);?>);">
                <div class="container"> 
                    <div class="content">
                        <div class="content-title">
                            <?php insert_acf($titulo, 'h2'); ?>
                        </div>
                        <div class="numbers">
                            <div class="row">
                                <?php if ( have_rows( 'items' ) ) : ?>
                                    <?php $i= 1; ?>
                                    <?php while ( have_rows( 'items' ) ) :
                                    the_row(); ?>
                                    <?php 
                                        $texto = get_sub_field( 'texto' );
                                        $subfijo = get_sub_field( 'subfijo' );
                                        $numero = get_sub_field( 'numero' );
                                        $texto_2 = get_sub_field( 'texto_2' );
                                    ?>
                                    <div class="col-sm-4 col-12">
                                        <div class="count">
                                            <?php
                                                insert_acf($texto, 'p', 'text-top');
                                            ?>
                                            <?php if($numero): ?>
                                                <span class="number counter" id="counter-<?php echo $i++?>" data-sign="<?php echo $subfijo?>" data-value="<?php echo $numero?>"><?php echo $subfijo?><?php echo $numero ?></span>
                                            <?php endif; ?>
                                            <?php insert_acf($texto_2, 'p', 'text-bottom'); ?>
                                        </div>
                                    </div>
                                    <?php endwhile; ?>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </section>

        <?php endwhile; ?>
    <?php endif; ?>
</main>

<?php get_footer(); ?>