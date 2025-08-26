<?php get_header(); ?>

<?php 
    $portada_img = get_the_post_thumbnail_url();
    $developments_under_construccion = filter_developments([], ['construction_status' => 4], 3);
    $property_feature = [];
    $params = [
    'data' => [
        'current_localization_id' => 1, // ID de ciudad/localidad
        'current_localization_type' => 'country', // "country", "province", "city", "zone"
        'price_from' => 0,
        'price_to' => 99999999999999,
        'operation_types' => [1,2], // 1 = Venta
        'property_types' => range(1, 28),  // 2 = Departamento (por ejemplo)
        'currency' => 'USD',
        'filters' => [["is_starred_on_web","=",'true']],
        'with_tags' => []
        ]
    ];
    $propertys = get_all_property_by_filter($params, 4, 0, 'created_at', 'DESC');


?>



<main class="front-page" data-current="<?php echo return_url();?>">
    <?php if ( have_rows( 'portada' ) ) : ?>

        <?php while ( have_rows( 'portada' ) ) :

        the_row(); ?>

            <?php

                $imagen_mobile = get_sub_field( 'imagen' )['url'] ?? $portada_img; 
                $ocultar = get_sub_field( 'ocultar' );
                $class = $ocultar ? 'd-none' : '';
            ?> 



                <section class="hero <?php echo $class?>" 

                data-image-desktop="<?php echo esc_url( $portada_img ); ?>" 

                data-image-mobile="<?php echo esc_url( $imagen_mobile ); ?>" 

                style="--background-image-desktop: url(<?php echo esc_url( $portada_img ); ?>);

                --background-image-mobile: url(<?php echo esc_url( $imagen_mobile ); ?>);"

                >

                    <div class="container">

                        <div class="content">

                            <?php  $titulo = get_sub_field('titulo'); ?>

                            <?php  $texto = get_sub_field('texto'); ?>

                            <?php insert_acf($texto, 'p'); ?>

                            <?php insert_acf($titulo, 'h1'); ?>

                            <?php get_template_part('/template-parts/content', 'search');?>

                        </div>

                    </div>

                </section>

            <?php endwhile; ?>

    <?php endif; ?>
    <?php if ( have_rows( 'categorias_destacadas' ) ) : ?>
            <?php while ( have_rows( 'categorias_destacadas' ) ) :
                    the_row(); ?>
            <?php $ocultar = get_sub_field( 'ocultar' );
                $class = $ocultar ? 'd-none' : ''; ?>
  
            <section class="category-featured <?php echo $class;?>">
                <div class="container">
                    <?php  $titulo = get_sub_field('titulo'); ?>
                    <?php  $link = get_sub_field( 'cta' ); ?>
                    <div class="header-title">
                        <?php insert_acf($titulo, 'h2'); ?>
                        <?php if($link): ?>
                        <div class="button__container d-flex-md d-none">
                            <a href="<?php echo esc_url($link['url']); ?>" class="btn btn-red-outline" title="Ver mas">
                                Ver todos
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <?php if ( have_rows( 'categoria' ) ) : ?>
                            <?php while ( have_rows( 'categoria' ) ) :
                            the_row(); ?>
                                <?php 
                                    $title = get_sub_field( 'titulo' );
                                    $image = get_sub_field('imagen');
                                    $image_mobile = get_sub_field('imagen_mobile');
                                    $url_parametros = get_sub_field( 'link' );
                                ?>
      
                                <div class="col-sm-4 col-xs-6 col-12">
                                    <a href="<?php echo esc_url($url_parametros);?>" class="card-category" title="Ver categoria <?php echo $title;?>" target="_blank"> 
                                        <div class="image d-flex-sm d-none">
                                            <?php insert_image($image); ?>
                                        </div>
                                        <div class="image d-none-sm">
                                            <?php insert_image($image_mobile); ?>
                                        </div>
                                        <div class="content">
                                            <?php insert_acf($title, 'h3', 'title') ?>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <div class="button__container d-none-md d-flex justify-center align-items-center mt-4">
                        <?php if($link ): ?>
                            <a href="<?php echo esc_url($link['url']); ?>" class="btn btn-red-outline" title="Ver todas las propiedades">
                                Ver todos
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
            <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'emprendimientos' ) ) : ?>
        <?php while ( have_rows( 'emprendimientos' ) ) :
        the_row(); ?>
            <?php $ocultar = get_sub_field( 'ocultar' );
                $class = $ocultar ? 'd-none' : ''; ?>
            <section class="developments-feature <?php echo $class;?>">
                <div class="container">
                    <?php  $titulo = get_sub_field('titulo'); ?>
                    <div class="header-title">
                        <?php insert_acf($titulo, 'h2'); ?>
                        <div class="button__container d-flex-md d-none">
                            <a href="<?php echo return_url() . '/emprendimientos/' ?>" class="btn btn-red-outline" title="Ver mas">
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
                                $permalink = return_url() . '/emprendimiento/' . slugify($item['name']);
                                $post = get_page_by_path(slugify($item['name']), OBJECT, 'emprendimiento');

                                $object_position = 'center';
                                if ($post) {
                                    $object_position = get_field('imagen', $post->ID) ?? 'center'; 
                                }
                            ?>  
                            <div class="col-sm-4 col-xs-6 col-12">
                                <a href="<?php echo $permalink;?>" class="card-feature" title="Emprendimiento - <?php echo $name;?>"> 
                                    <div class="image">
                                        <img <?php echo get_object_position($object_position);?> src="<?php echo $image;?>" alt="<?php echo $name;?>" width="100%" height="100%" loading="lazy" alt="En la imagen se muestra el emprendimiento - <?php echo $name?>">
                                    </div>
                                    <div class="content">
                                        <span class="status"><?php echo  $status;?></span>
                                        <h3 class="title"><?php echo  $name;?></h3>
                                        <p class="location"><?php echo  $location;?></p>
                                    </div>
                                </a>
                            </div>
                            <?php wp_reset_postdata(); ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="button__container d-none-md d-flex justify-center align-items-center mt-4">
                        <a href="<?php echo return_url() . '/emprendimientos/';?>" class="btn btn-red-outline" title="Ver mas">
                            Ver todos
                        </a>
                    </div>
                </div>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'propiedades_destacadas' ) ) : ?>
        <?php while ( have_rows( 'propiedades_destacadas' ) ) :
        the_row(); ?>
            <?php $ocultar = get_sub_field( 'ocultar' );
                $class = $ocultar ? 'd-none' : ''; ?>
            <section class="property-feature <?php echo $class;?>">
                <div class="container">
                    <?php  $titulo = get_sub_field('titulo'); ?>
                    <?php  $link = get_sub_field( 'cta' ); ?>
                    <div class="header-title">
                        <?php insert_acf($titulo, 'h2'); ?>
                        <div class="button__container d-flex-md d-none">
                            <?php if($link ): ?>
                                <a href="<?php echo esc_url($link['url']); ?>" class="btn btn-red-outline" title="Ver todas las propiedades">
                                    Ver todos
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                                <?php foreach ($propertys['objects'] ?? [] as $property): ?>
                                <?php 
                                    $feature = $property['is_starred_on_web'];
                                    $name = $property['name'] ?? '';
                                    $image = $property['photos'][0]['image'];
                                    $location = get_full_location($property['location']['full_location']);
                                    $currency_price = $property['operations'][0]['prices'][0]['currency'];
                                    if(count($property['operations']) === 2){
                                        if($operacion_slug === '2'){
                                            $price = $property['operations'][1]['prices'][0]['price'];
                                            $operation_type = $property['operations'][1]['operation_type'];
                                        }else{
                                            $price = $property['operations'][0]['prices'][0]['price'];
                                            $operation_type = $property['operations'][0]['operation_type'];
                                        }
                                    }else{
                                        $price = $property['operations'][0]['prices'][0]['price'];
                                        $operation_type = $property['operations'][0]['operation_type'];
                                    }
                                    $total_price = render_price_format($price, $currency_price);
                                    $type_property = $property['type']['name'];
                                    $address = $property['address'];
                                    $total_surface = $property['total_surface'];
                                    $bathroom_amount = $property['bathroom_amount'];
                                    $permalink = return_url() . '/propiedad/' . slugify($address) . '/' . $property['id'];
                                    $created_at = $property['created_at'];
                                ?>
                                <div class="col-xxl-3 col-md-4 col-xs-6 col-12"  data-date = "<?php echo $created_at;?>" data-fetaure="<?php echo $feature;?>">
                                    <a  class="card-property" href="<?php echo $permalink?>"
                                        title="<?php echo $operation_type;?> / <?php echo $type_property;?> -  <?php echo $total_price;?>  - <?php echo  $address;?> - <?php echo $location;?>"
                                        target="_blank">
                                        <div class="image">

                                            <button type="button" class="favorite">

                                                <?php render_svg(SVG . '/icon-favorite.svg'); ?>

                                            </button>

                                            <img src="<?php echo $image;?>" alt="<?php echo $name;?>" width="100%" height="100%"

                                                loading="lazy">

                                            <div class="price">

                                                <p class="type"><?php echo $operation_type;?> / <?php echo $type_property;?></p>

                                                <p class="amount"><?php echo $total_price;?></p>

                                            </div>

                                        </div>

                                        <div class="body">

                                            <div class="location">

                                                <p class="name"><?php echo  $address;?></p>

                                                <p class="district"><?php echo $location;?></p>

                                            </div>

                                            <div class="specs">

                                                <p class="total"><?php echo $total_surface;?></p>

                                                <p class="bathroom_amount"><?php echo $bathroom_amount;?></p>

                                                <p>M<sup>2</sup></p>

                                                <p>Baños</p>

                                            </div>

                                        </div>

                                    </a>

                                </div>
                                <?php endforeach; ?>
                    </div>
                    <div class="button__container d-none-md d-flex justify-center align-items-center mt-4">
                        <?php if($link ): ?>
                        <a href="<?php echo esc_url($link['url']); ?>" class="btn btn-red-outline" title="Ver todas las propiedades">
                            Ver todos
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php if ( have_rows( 'propiedades' ) ) : ?>
        <?php while ( have_rows( 'propiedades' ) ) :
        the_row(); ?>
            <?php $ocultar = get_sub_field( 'ocultar' );
                $class = $ocultar ? 'd-none' : ''; ?>
            <section class="property <?php echo $class;?>">
                <div class="container">
                    <?php  $titulo = get_sub_field('titulo'); ?>
                    <div class="header-title">
                        <?php insert_acf($titulo, 'h2'); ?>
                        <div class="button__container d-flex-md d-none">
                            <?php $url = get_sub_field( 'url' ); ?>
                            <a href="<?php echo esc_url($url);?>" class="btn btn-red-outline" title="Ver mas" target="_blank">
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
                                    $url_parametros = get_sub_field( 'url_parametros' );
                                ?>

                                <div class="col-sm-4 col-6">
                                    <a href="<?php echo esc_url($url_parametros);?>" class="card-category-property" title="Ver categoria <?php echo $title;?>" target="_blank"> 
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