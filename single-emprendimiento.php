<?php

$current_id = $_GET['id'] ?? get_field('id') ?? 0;

$primary = get_development_by_id($current_id);

$development = null;
if (is_array($primary) && isset($primary['objects']) && !empty($primary['objects'])) {
    $development = $primary['objects'][0];
}

if (!$development) {
    $development = get_detail_development_by_json($current_id);
}



$hero_image = get_hero_image_development($development);
$images = get_images_development($development);
$blue_print_images = get_images_blue_print($development);

// INFO
$name = $development['name'];
$location = get_full_location($development['location']['full_location']);
$type = $development['type']['name'];
$status = get_construction_status($development['construction_status']);
$units = get_development_units($current_id)['meta']['total_count'] ?? 0;
$construction_date = $development['construction_date'];
$publication_title = $development['publication_title'];
$description = $development['description'];
$tags = $development['tags'];

// Videos
$video_url = fix_youtube_embed_url($development['videos'][0]['player_url'] ?? '');
$video_title = $development['videos'][0]['title'] ?? '';

// Ubicación
$address_development = $development['address'];

// Geo
$lat = $development['geo_lat'];
$long = $development['geo_long'];

// Get all properties by id development
$params = [
    'data' => [
        'current_localization_id' => 1,
        'current_localization_type' => 'country',
        'price_from' => 1,
        'price_to' => 999999999999,
        'operation_types' => [1, 2, 3],
        'property_types' => range(1, 25),
        'currency' => 'ANY',
        'filters' => [
            ["development__id", "=", $current_id]
        ]
    ]
];

$unidades = get_all_property_by_filter($params, 100, 0);

if (isset($unidades['objects']) && is_array($unidades['objects'])) {
    usort($unidades['objects'], function($a, $b) {
        $floorA = extractFloorIdentifier($a['real_address']);
        $floorB = extractFloorIdentifier($b['real_address']);
        if ($floorA === null && $floorB === null) return 0;
        if ($floorA === null) return 1;
        if ($floorB === null) return -1;
        return $floorA <=> $floorB;
    });
} else {
    $unidades['objects'] = [];
}

// Obtener precio máximo y mínimo
$prices = get_min_max_price($unidades);

$hay_planos = get_field('activar') ? true : false;

?>

<?php get_header(); ?>

<main class="template-emprendimiento " id="<?php echo $current_id?>">

    <section class="section-1 p-relative d-flex align-items-end justify-end <?php echo $current_id; ?>">

        <img src="<?php echo $hero_image['image'] ?>" class="img-cover" alt="">

        <div class="container p-relative">

            <h1><?php echo $name;?></h1>

            <h2><?php echo $location; ?></h2>

            <?php if($unidades['meta']['total_count'] > 0): ?>

            <div class="prices-global d-flex-md d-none">

                <div class="min">

                    <p>DESDE</p>

                    <span><?php echo esc_html($prices['prices']['min_price'])?></span>

                </div>

                <div class="max">

                    <p>HASTA</p>

                    <span><?php echo esc_html($prices['prices']['max_price']);?></span>

                </div>

            </div>

            <?php endif; ?>
            <?php if ( $hay_planos ) : ?>
 
                     <button class="btn btn-plano btn-stroke-white btn-icon d-flex align-items-center p-absolute d-none d-flex-md align-items-center btnModal">

                        <?php render_svg(SVG . '/icon-development-plano.svg'); ?>

                        Ver plano

                    </button>

            <?php endif; ?>
 

        </div>

    </section>

    <section class="section-prices">

        <div class="container">

        <div class="prices-global d-none-md">

            <div class="min">

                <p>DESDE</p>

                <span><?php echo esc_html($prices['prices']['min_price']);?></span>

            </div>

            <div class="max">

                <p>HASTA</p>

                <span><?php echo esc_html($prices['prices']['max_price']);?></span>

            </div>

        </div>

        </div>

    </section>

    <section class="section-2">

        <div class="container d-flex align-items-center justify-between">

            <div class="d-flex align-items-start dato">

                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="33" viewBox="0 0 35 33" fill="none">

                    <path d="M3.7168 7.07676V31.4407H31.5613V3.59619" stroke="#E40729" stroke-width="2.5"

                        stroke-linecap="round" stroke-linejoin="round" />

                    <path d="M21.1232 31.441V21.9342C21.1232 18.2041 14.1621 18.5344 14.1621 21.9342V31.441"

                        stroke="#E40729" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                    <path

                        d="M1.97754 7.07656L15.4388 2.30843C17.6208 1.70481 17.6594 1.70481 19.8414 2.30843L33.3026 7.07656"

                        stroke="#E40729" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                    <path d="M17.6611 12.2974H17.6406" stroke="#E40729" stroke-width="2.5" stroke-linecap="round"

                        stroke-linejoin="round" />

                </svg>

                <div class="d-flex flex-column align-items-start">

                    <h3>Tipo</h3>

                    <p><?php echo $type;?></p>

                </div>



            </div>

            <div class="hr"></div>

            <div class="d-flex align-items-start dato">

                <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">

                    <path

                        d="M15.6567 22.1369L24.7796 31.2598C25.562 32.0172 26.6108 32.4368 27.6997 32.4279C28.7886 32.4191 29.8305 31.9826 30.6005 31.2126C31.3705 30.4426 31.807 29.4008 31.8158 28.3118C31.8246 27.2229 31.4051 26.1742 30.6477 25.3918L21.4513 16.1953M15.6567 22.1369L19.5625 17.3955C20.0586 16.7946 20.7205 16.4159 21.4528 16.1969C22.3135 15.9402 23.2727 15.9027 24.1803 15.9778C25.4037 16.0828 26.6334 15.8662 27.7472 15.3492C28.861 14.8323 29.8203 14.0331 30.5298 13.0309C31.2394 12.0288 31.6745 10.8585 31.7921 9.63619C31.9097 8.41391 31.7056 7.18213 31.2001 6.06308L26.0737 11.191C25.2161 10.9927 24.4313 10.5575 23.8089 9.93504C23.1864 9.31257 22.7512 8.52782 22.5529 7.67015L27.6792 2.5438C26.5602 2.0383 25.3284 1.83422 24.1061 1.9518C22.8839 2.06938 21.7136 2.50454 20.7114 3.21409C19.7092 3.92364 18.91 4.88291 18.3931 5.99673C17.8762 7.11054 17.6595 8.34017 17.7645 9.56359C17.9069 11.2473 17.6534 13.1063 16.3499 14.1798L16.1903 14.3128M15.6567 22.1369L8.37248 30.9829C8.01945 31.4132 7.58022 31.7649 7.08306 32.0152C6.58591 32.2655 6.04185 32.409 5.48591 32.4363C4.92996 32.4637 4.37444 32.3744 3.85511 32.1741C3.33578 31.9738 2.86414 31.6669 2.47055 31.2733C2.07696 30.8798 1.77014 30.4081 1.56984 29.8888C1.36954 29.3695 1.28019 28.8139 1.30756 28.258C1.33492 27.702 1.47838 27.158 1.72871 26.6608C1.97904 26.1637 2.3307 25.7244 2.76103 25.3714L13.4597 16.5615L7.033 10.1347H4.82816L1.30731 4.26667L3.65455 1.91943L9.52263 5.44028V7.64512L16.1888 14.3113L13.4582 16.5599M26.5401 27.1522L22.4324 23.0445M5.40245 28.3258H5.41497V28.3383H5.40245V28.3258Z"

                        stroke="#E40729" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                </svg>

                <div class="d-flex flex-column align-items-start dato">

                    <h3>Estado</h3>

                    <p><?php echo $status;?></p>

                </div>

            </div>

            <?php if($unidades['meta']['total_count']): ?>

                <div class="hr"></div>

                <div class="d-flex align-items-start dato">

                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="34" viewBox="0 0 31 34" fill="none">

                        <path

                            d="M1.82422 31.7667H29.541M3.08407 1.53021H28.2812M4.34393 1.53021V31.7667M27.0213 1.53021V31.7667M10.6432 7.82948H13.1629M10.6432 12.8689H13.1629M10.6432 17.9083H13.1629M18.2023 7.82948H20.722M18.2023 12.8689H20.722M18.2023 17.9083H20.722M10.6432 31.7667V26.0974C10.6432 25.0542 11.4898 24.2076 12.533 24.2076H18.8323C19.8754 24.2076 20.722 25.0542 20.722 26.0974V31.7667"

                            stroke="#E40729" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />

                    </svg>

                    <div>

                        

                        <h3><?php echo ($unidades['meta']['total_count'] < 1 ? 'Disponible' : 'Disponibles'); ?></h3>

                        <p> <?php echo $unidades['meta']['total_count']; ?> </p>

                    </div>

                </div>

            <?php endif; ?>

            <div class="hr"></div>

            <div class="d-flex align-items-start dato">

                <svg width="34" height="33" viewBox="0 0 34 33" fill="none" xmlns="http://www.w3.org/2000/svg">

                <path d="M7.94727 1.75879V5.50879M25.8848 1.75879V5.50879M1.54102 28.0088V9.25879C1.54102 8.26423 1.94598 7.3104 2.66682 6.60714C3.38767 5.90388 4.36534 5.50879 5.38477 5.50879H28.4473C29.4667 5.50879 30.4444 5.90388 31.1652 6.60714C31.8861 7.3104 32.291 8.26423 32.291 9.25879V28.0088M1.54102 28.0088C1.54102 29.0034 1.94598 29.9572 2.66682 30.6604C3.38767 31.3637 4.36534 31.7588 5.38477 31.7588H28.4473C29.4667 31.7588 30.4444 31.3637 31.1652 30.6604C31.8861 29.9572 32.291 29.0034 32.291 28.0088M1.54102 28.0088V15.5088C1.54102 14.5142 1.94598 13.5604 2.66682 12.8571C3.38767 12.1539 4.36534 11.7588 5.38477 11.7588H28.4473C29.4667 11.7588 30.4444 12.1539 31.1652 12.8571C31.8861 13.5604 32.291 14.5142 32.291 15.5088V28.0088M16.916 18.0088H16.9297V18.0221H16.916V18.0088ZM16.916 21.7588H16.9297V21.7721H16.916V21.7588ZM16.916 25.5088H16.9297V25.5221H16.916V25.5088ZM13.0723 21.7588H13.0859V21.7721H13.0723V21.7588ZM13.0723 25.5088H13.0859V25.5221H13.0723V25.5088ZM9.22852 21.7588H9.24218V21.7721H9.22852V21.7588ZM9.22852 25.5088H9.24218V25.5221H9.22852V25.5088ZM20.7598 18.0088H20.7734V18.0221H20.7598V18.0088ZM20.7598 21.7588H20.7734V21.7721H20.7598V21.7588ZM20.7598 25.5088H20.7734V25.5221H20.7598V25.5088ZM24.6035 18.0088H24.6172V18.0221H24.6035V18.0088ZM24.6035 21.7588H24.6172V21.7721H24.6035V21.7588Z" stroke="#E40729" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

                </svg>



                <div>

                    <h3>Entrega</h3>

                    <?php render_construction_date($construction_date); ?>

                </div>

            </div>
            <?php if ( $hay_planos ) : ?>

                    <button class="btn btn-plano btn-stroke-black btn-icon d-flex align-items-center p-absolute d-none-md d-flex align-items-center btn-modal">

                        <?php render_svg(SVG . '/icon-development-plano.svg'); ?>

                        Ver plano

                    </button>

            <?php endif; ?>


        </div>

    </section>

    <section class="section-3">

        <div class="container-fluid">

            <div class="row">

                <div class="col-md-6 col-sm-5 col-12">

                    <div class="cont-img">

                        <img src="<?php echo $images[1]['image']; ?>" class="img-cover" alt="">

                    </div>

                </div>

                <div class="col-md-6 col-sm-7 col-12">

                    <div class="cont-info d-flex flex-column">

                        <?php if($publication_title): ?>

                            <div class="info">

                                <h2>Concepto</h2>

                                <p><?php echo $publication_title; ?></p>

                            </div>

                        <?php endif; ?>

                        <?php if($description): ?>

                            <div class="info">

                                <h2>Descripcion</h2>

                                <p><?php echo nl2br($description); ?></p>

                            </div>

                        <?php endif; ?>

                        <?php if($tags): ?>

                            <div class="info">

                                <h2>Amenities</h2>

                                <ul>

                    

                                    <?php foreach ($tags as $tag) { ?>

                        

                                        <?php if ($tag['type'] == 3): ?>

                                            <li>

                                                <?= $tag['name']; ?>

                                            </li>

                                        <?php endif; ?>

                                        <?php

                                    } ?>

                                </ul>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <?php if($images): ?>

        <section class="section-4">

            <div class="container d-flex justify-center">

                <div class="splide" id="galeriaDevelopment">

                    <div class="splide__track">

                        <ul class="splide__list">

                            <?php foreach($images as $img): ?>
                                <?php 

                                    $description = $img['description'];

                                    $image = $img['image'];

                                    $blue_print = $img['is_blueprint'];

                                ?>
                                <?php if(!$blue_print): ?>

                                    <li class="splide__slide">

                                        <a data-blue="<?php echo $img['is_blueprint']?>" href="<?php echo esc_url($image);?>" data-fancybox="galeria" class="item">

                                            <img src="<?php echo esc_url($image);?>" alt="<?php echo esc_html($description);?>">

                                        </a>

                                    </li>

                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>

                    </div>

                </div>

            </div>

        </section>

    <?php endif; ?>

    <?php if($unidades['meta']['total_count'] > 0): ?>

        <section class="section-5">
            <div class="container">
                <div class="d-flex flex-column align-items-center justify-between w-100">

                    <?php
                        $unidades_flat = $unidades['objects'] ?? [];
                        $unidades_por_ambiente = [];

                        foreach ($unidades_flat as $unidad) {
                            $ambientes = (int)($unidad['room_amount'] ?? 0);
                            $unidades_por_ambiente[$ambientes][] = $unidad;
                        }

                        ksort($unidades_por_ambiente);
                        $todas_las_unidades = array_merge(...array_values($unidades_por_ambiente));
                    ?>

                    <div class="header-section-5 d-flex justify-between w-100 align-items-center w-100">
                        <h2>Unidades disponibles</h2>

                        <!-- TABS: encabezado -->
                        <div class="tabs-nav d-none d-flex-md align-items-center justify-between">
                            <div class="btn-unidad active">
                                <p data-panel="tab-todos">Ver todos</p>
                            </div>
                            <div class="hr"></div>

                            <?php foreach ($unidades_por_ambiente as $ambientes => $grupo): ?>
                                <div class="btn-unidad">
                                    <p data-panel="tab-<?= $ambientes ?>">
                                        <?= $ambientes === 0 ? 'Sin info' : $ambientes . ' ambiente' . ($ambientes > 1 ? 's' : '') ?>
                                    </p>
                                </div>
                                <div class="hr"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Dropdown para mobile -->
                    <div class="tabs-dropdown d-block d-none-md">
                        <div class="select-unidades">
                            <div class="field-container-input__icon">
                                <i class="fa-solid fa-chevron-down"></i>
                            </div>
                            <input type="text" readonly value="" id="select-unidades-input">

                            <div class="list-select">
                                <ul>
                                    <li class="options-list-select btn-unidad">
                                        <p data-panel="tab-todos">Ver todos</p>
                                    </li>
                                    <?php foreach ($unidades_por_ambiente as $ambientes => $grupo): ?>
                                        <li class="options-list-select btn-unidad">
                                            <p data-panel="tab-<?= $ambientes ?>">
                                                <?= $ambientes === 0 ? 'Sin info' : $ambientes . ' ambientes' ?>
                                            </p>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- TABS: contenido -->
            <div class="tabs-content w-100">

                <!-- TAB TODOS -->
                <div id="tab-todos" class="tab tab-panel active">
                    <div class="tabla">
                        <div class="tabla-header">
                            <div class="col">Piso</div>
                            <div class="col d-none d-block-md">Ambientes</div>
                            <div class="col d-none-md d-block">Amb.</div>
                            <div class="col d-none d-block-md">Sup. Interior</div>
                            <div class="col">Sup. Total</div>
                            <div class="col">Precio</div>
                            <div class="col d-none-md acc">ACC</div>
                        </div>

                        <?php foreach ($todas_las_unidades as $unidad): ?>
                            <?php
                                $currency_price = $unidad['operations'][0]['prices'][0]['currency'];
                                $price = $unidad['operations'][0]['prices'][0]['price'];
                                $total_price = render_price_format($price, $currency_price);
                                $address = $unidad['address'];
                                $permalink = return_url() . '/propiedad/' . slugify($address) . '/' . $unidad['id'];
                                $real_adress = $unidad['real_address'];
                                $floor = extractFloorIdentifier($real_adress);
                            ?>
                            <div class="tabla-row">
                                <div class="col"><?= $floor ?? '—' ?>&#176;</div>
                                <div class="col"><?= $unidad['room_amount'] ?? '—' ?></div>
                                <div class="col d-none d-block-md"><?= $unidad['roofed_surface'] ?? '—' ?> m²</div>
                                <div class="col"><?= $unidad['total_surface'] ?? '—' ?> m²</div>
                                <div class="col"><?= !empty($unidad['web_price']) ? $total_price : 'Consultar' ?></div>
                                <div class="col d-none d-block-md">
                                    <a href="<?= $permalink ?>" target="_blank" class="btn btn-black-white">Ver</a>
                                </div>
                                <div class="col d-none-md d-block">
                                    <a href="<?= $permalink ?>" target="_blank" class="btn btn-black-white"><i class="fa-solid fa-plus"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- TABS POR AMBIENTES -->
                <?php foreach ($unidades_por_ambiente as $rooms => $grupo): ?>
                    <div id="tab-<?= $rooms ?>" class="tab tab-panel">
                        <div class="tabla">
                            <div class="tabla-header">
                                <div class="col">Piso</div>
                                <div class="col d-none d-block-md">Ambientes</div>
                                <div class="col d-none-md d-block">Amb.</div>
                                <div class="col d-none d-block-md">Sup. Interior</div>
                                <div class="col">Sup. Total</div>
                                <div class="col">Precio</div>
                                <div class="col d-none-md acc">ACC</div>
                            </div>

                            <?php foreach ($grupo as $unidad): ?>
                                <?php
                                    $currency_price = $unidad['operations'][0]['prices'][0]['currency'];
                                    $price = $unidad['operations'][0]['prices'][0]['price'];
                                    $total_price = render_price_format($price, $currency_price);
                                    $address = $unidad['address'];
                                    $permalink = return_url() . '/propiedad/' . slugify($address) . '/' . $unidad['id'];
                                    $real_adress = $unidad['real_address'];
                                    $floor = extractFloorIdentifier($real_adress);
                                ?>
                                <div class="tabla-row">
                                    <div class="col"><?= $floor ?? '—' ?>&#176;</div>
                                    <div class="col"><?= $unidad['room_amount'] ?? '—' ?></div>
                                    <div class="col d-none d-block-md"><?= $unidad['roofed_surface'] ?? '—' ?> m²</div>
                                    <div class="col"><?= $unidad['total_surface'] ?? '—' ?> m²</div>
                                    <div class="col"><?= !empty($unidad['web_price']) ? $total_price : 'Consultar' ?></div>
                                    <div class="col d-none d-block-md">
                                        <a href="<?= $permalink ?>" target="_blank" class="btn btn-black-white">Ver</a>
                                    </div>
                                    <div class="col d-none-md d-block">
                                        <a href="<?= $permalink ?>" target="_blank" class="btn btn-black-white"><i class="fa-solid fa-plus"></i></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    <?php endif; ?>       

    <?php if($video_url): ?>

        <section class="section-video">

            <div class="container">

                <div class="video p-relative">

                    <iframe src="<?php echo esc_url($video_url);?>" 

                            frameborder="0" 

                            controls="true"

                            allowfullscreen 

                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">

                    </iframe>

                </div>

            </div>

        </section>

    <?php endif; ?>     

    <section class="section-6">

        <div class="container">

            <h2>Ubicación</h2>

            <p><?php echo esc_html($address_development);?>, <?php echo esc_html($location);?></p>

            <div class="mapViewProperty">

                <div id="mapviewproperty" class="mapViewSingle" data-lat="<?php echo esc_html($lat);?>" data-long="<?php echo esc_html($long);?>"></div>

            </div>

        </div>

    </section>       

    <section class="section-7">

        <div class="container d-flex flex-row-md flex-column justify-between align-items-start">

            <div class="cont-texto">

                <h2><strong>¿Querés más detalles </strong> <br> de este emprendimiento?</h2>

                <p>Para más información completá el formulario de consultas</p>

            </div>

            <div class="cont-form">

                <form action="" data-origen="Form Emprendimiento" class="w-100 form-contacto p-relative" id="singleFormDevelopment" data-property="<?php echo $current_id?>" data-tags="Consulta de emprendimiento" data-event="form_emprendimiento">

                    <input type="hidden" value="<?php echo esc_url(home_url( add_query_arg( null, null ) )); ?>" id="propertyName">

                    <input type="hidden" value="<?php echo esc_html($type); ?>"  id="propertyType">

                    <input type="hidden" value="Venta" id="propertyOperation">

                    <input type="hidden" value="<?php echo $name;?>" id="developmentName">

                    <div class="d-flex flex-column align-items-start">

                        <label for="fullname">Nombre y apellido</label>

                        <input type="text" id="fullname">

                    </div>

                    <div class="d-flex flex-column align-items-start">

                        <label for="tetelephonel">teléfono</label>

                        <input type="tel" id="telephone">

                    </div>

                    <div class="d-flex flex-column align-items-start">

                        <label for="email">correo electrónico</label>

                        <input type="email" id="email">

                    </div>

                    <div class="d-flex flex-column align-items-start"> 

                        <label for="message">Mensaje</label>

                        <textarea name="" id="message"></textarea>

                    </div>

                    <button type="submit" class="btn btn-black">Enviar</button>

                    <div class="loading-form"><span class="loader"></span></div>

                </form>

            </div>

        </div>

    </section>  
    <?php if($hay_planos): ?>
    
        <?php get_template_part('template-parts/content', 'plano' );?>
    <?php endif;?>
</main>





<?php get_footer(); ?>