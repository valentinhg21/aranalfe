<?php

$current_id = get_query_var('propiedad_id');

$propertyData = get_details_property($current_id);

$property = $propertyData['objects'][0] ?? null;

if (!$property) {
    echo '<p>Cargando propiedad, por favor espere...</p>';
    echo '<script>setTimeout(() => location.reload(), 1500);</script>';
    exit;
}

$images = get_images_details_property_from_data($propertyData);

// Precio

$currency_price = $property['operations'][0]['prices'][0]['currency'] ?? '';
$price = $property['operations'][0]['prices'][0]['price'] ?? 0;
$price_max = round($price * 2);


$precios_ordenados = sort_prices_by_operation($property['operations']);
$count_prices = count($precios_ordenados ?? []);
$render_precios = render_all_prices_formatted($precios_ordenados);
$total_price = $render_precios[0]['formatted_price'];
$total_price_alquiler = '';
if($count_prices > 1){
    $total_price_alquiler = $render_precios[1]['formatted_price'];
}



// Tipo operación
$operation_type = $property['operations'][0]['operation_type'] ?? '';
$operation_type_id = $operation_type === 'Venta' ? 1 : ($operation_type === 'Alquiler' ? 2 : null);

// Tipo propiedad
$type_property = $property['type']['name'] ?? '';
$type_property_id = $property['type']['id'] ?? null;

// Dimensiones
$total_surface = $property['total_surface'] ?? null;
$semi_surface = $property['semiroofed_surface'] ?? null;
$roofed_surface = $property['roofed_surface'] ?? null;

// Ambientes
$room_amount = $property['room_amount'] ?? null;
$suite_amount = $property['suite_amount'] ?? null;
$bathroom_amount = $property['bathroom_amount'] ?? null;

// Ubicación
$address = $property['address'] ?? '';
$address_id = $property['location']['id'] ?? null;
$location = get_full_location($property['location']['full_location'] ?? '');

// Antigüedad
$age = $property['age'] ?? null;
$age_text = '';
if ($age !== null) {
    switch ($age) {
        case 0:
            $age_text = 'A estrenar';
            break;
        case -1:
            $age_text = 'En construcción';
            break;
        case 1:
            $age_text = '1 año';
            break;
        default:
            if ($age > 1) {
                $age_text = $age . ' años';
            }
            break;
    }
}

// Descripción
$concepto = $property['publication_title'] ?? '';
$description_text = $property['rich_description'] ?? '';


// Servicios
$servicios_only = [];
$servicios_ambientes = [];
$servicios_adicionales = [];
$servicios = $property['tags'] ?? [];

foreach ($servicios as $tag) {
    switch ($tag['type'] ?? 0) {
        case 1:
            $servicios_only[] = $tag;
            break;
        case 2:
            $servicios_ambientes[] = $tag;
            break;
        default:
            if (($tag['type'] ?? 0) > 2) {
                $servicios_adicionales[] = $tag;
            }
            break;
    }
}

// Geo
$lat = $property['geo_lat'] ?? null;
$long = $property['geo_long'] ?? null;

// Videos
$video_url = fix_youtube_embed_url($property['videos'][0]['player_url'] ?? '') ?? '';
$video_title = $property['videos'][0]['title'] ?? '';

// Propiedades similares
$params = [
    'data' => [
        'current_localization_id' => 0,
        'current_localization_type' => 'country',
        'price_from' => 0,
        'price_to' => $price_max,
        'operation_types' => $operation_type_id ? [$operation_type_id] : [],
        'property_types' => range(1, 28),
        'currency' => $currency_price,
        'filters' => [],
        'with_tags' => []
    ]
];

$property_similar = get_all_property_by_filter($params, 4, 0);
$property_similar_filter = array_values(array_filter($property_similar['objects'] ?? [], function($prop) use ($current_id) {
    return $prop['id'] != $current_id;
}));

$class_property_similar = count($property_similar_filter) <= 3
    ? 'col-xxl-4 col-md-4 col-sm-6 col-12'
    : 'col-xxl-3 col-md-4 col-sm-6 col-12';

// Meta SEO
$meta_title = trim("$type_property ubicado en $location en $operation_type | Aranalfe");
$meta_description = trim("$type_property ubicado en $location en $operation_type. Encontrá tu próxima propiedad con Aranalfe");
$meta_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
$meta_img = $images[0]['image'] ?? '';

get_header('', [
    'title' => $meta_title,
    'description' => $meta_description,
    'permalink' => $meta_link,
    'image' => $meta_img,
]);

?>

<main class="single-property" id="<?php echo esc_html($current_id);?>">
    <div class="container-fluid">
        <section class="hero">
            <div class="splide" id="hero-splide<?php echo count($images) <= 2 ? '-' . count($images) : ''; ?>" data-images="<?php echo count($images);?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php foreach($images as $img): ?>
                        <?php
                            $description = $img['description'];
                            $image = $img['image'];
                        ?>
                        <li class="splide__slide">
                            <a href="<?php echo esc_url($image);?>" data-fancybox="galeria" class="item">
                                <img src="<?php echo esc_url($image);?>" alt="<?php echo esc_html($description);?>">
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php if($video_url): ?>
            <div class="single-video-map single-video">
                <iframe src="<?php echo esc_url($video_url);?>"
                        frameborder="0"
                        controls="true"
                        allowfullscreen
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                </iframe>
            </div>
            <?php endif; ?>
            <div class="single-video-map single-map">
                <div id="mapSingleHero" data-lat="<?php echo esc_html($lat);?>" data-long="<?php echo esc_html($long);?>"></div>
            </div>
        </section>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-sm-7 col-12">
                <div class="body-content">
                    <div class="options-view">
                        <ul>
                            <li>
                                <button type="buttton" class="btn-view btn-open-image active">
                                    <?php render_svg(SVG . '/icon-image.svg');?> Fotos
                                </button>
                            </li>
                            <?php if($video_url): ?>
                            <li>
                                <button type="buttton" class="btn-view btn-open-video">
                                    <?php render_svg(SVG . '/icon-video.svg');?>
                                    Video
                                </button>
                            </li>
                            <?php endif; ?>
                            <li>
                                <button type="buttton" class="btn-view btn-open-single-map">
                                    <?php render_svg(SVG . '/icon-location.svg');?>
                                    Mapa
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="breadcrum">
                        <ul>
                            <li><?php echo esc_html($operation_type);?></li>
                            <li><?php echo esc_html($type_property);?></li>
                            <?php if($total_surface): ?>
                                <li><?php echo esc_html($total_surface);?> M<sup>2</sup></li>
                            <?php endif; ?>
                            <?php if($room_amount): ?>
                                <?php if($room_amount <= 1): ?>
                                        <li><?php echo esc_html($room_amount);?> Ambiente</li>
                                    <?php else: ?>
                                        <li><?php echo esc_html($room_amount);?> Ambientes</li>
                                <?php endif; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="info">
                        <?php if($count_prices == 1): ?>
                            <span><?php echo esc_html($total_price);?></span>
                            <?php else: ?>
                                <div class="info-prices">
                                    <div  class="price-usd">
                                        <p>PRECIO DE VENTA</p>
                                        <span><?php echo esc_html($total_price);?></span>
                                    </div>
                                    <div class="price-ars">
                                        <p>PRECIO DE ALQUILER</p>
                                        <span ><?php echo esc_html($total_price_alquiler);?></span>
                                    </div>
                                </div>
                        <?php endif; ?>
                        <h1><?php echo esc_html($address);?></h1>
                        <h2><?php echo esc_html($location);?></h2>
                    </div>
                    <div class="area">
                        <?php if($total_surface > 0): ?>
                        <div class="item">
                            <?php render_svg(SVG . '/icon-superficie.svg') ?>
                            <p><?php echo esc_html($total_surface);?> m<sup>2</sup> totales</p>
                        </div>
                        <?php endif; ?>
                        <?php if($roofed_surface > 0): ?>
                        <div class="item">
                            <?php render_svg(SVG . '/icon-semicubierto.svg') ?>
                            <p><?php echo esc_html($roofed_surface);?> m<sup>2</sup> cubiertos</p>
                        </div>
                        <?php endif; ?>
                        <?php if($semi_surface > 0): ?>
                        <div class="item">
                            <?php render_svg(SVG . '/icon-cubierto.svg') ?>
                            <p><?php echo esc_html($semi_surface);?> m<sup>2</sup> semicubiertos</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="details">
                        <div class="item">
                            <h2>Detalles de la propiedad</h2>
                            <ul class="list-mobile-area">
                                <?php if($total_surface > 0): ?>
                                    <li>
                                        <div class="icon">
                                                <?php render_svg(SVG . '/icon-superficie.svg') ?>
                                        </div>
                                        <p><?php echo esc_html($total_surface)?> m<sup>2</sup> tot. construído</p>
                                    </li>
                                <?php endif; ?>
                                <?php if($roofed_surface > 0): ?>
                                    <li>
                                        <div class="icon">
                                                <?php render_svg(SVG . '/icon-semicubierto.svg') ?>
                                        </div>
                                        <p><?php echo esc_html($roofed_surface);?> m<sup>2</sup> <?php echo ($roofed_surface < 2 ? 'cubierto' : 'cubiertos'); ?></p>
                                    </li>
                                <?php endif; ?>
                                <?php if($semi_surface > 0): ?>
                                    <li>
                                        <div class="icon"><?php render_svg(SVG . '/icon-cubierto.svg') ?></div>
                                        <p><?php echo esc_html($semi_surface);?> m<sup>2</sup> <?php echo ($roofed_surface < 2 ? 'semicubierto' : 'semicubiertos'); ?></p>
                                    </li>
                                <?php endif; ?>
                            </ul>
                            <ul class="list-details">
                                <?php if($room_amount): ?>
                                    <li>
                                        <div class="icon">
                                            <?php render_svg(SVG . '/icon-single-ambientes.svg'); ?>
                                        </div>
                                        <?php echo esc_html($room_amount); ?>
                                        <?php echo ($room_amount <= 1) ? 'Ambiente' : 'Ambientes'; ?>
                                    </li>
                                <?php endif; ?>
                                <?php if($suite_amount): ?>
                                    <li>
                                        <div class="icon">
                                            <?php render_svg(SVG . '/icon-single-dormitorio.svg'); ?>
                                        </div>
                                        <?php echo esc_html($suite_amount)?> <?php echo ($suite_amount <= 1) ? 'Dormitorio' : 'Dormitorios'; ?>
                                    </li>
                                <?php endif; ?>
                                <?php if($bathroom_amount): ?>
                                    <li>
                                        <div class="icon">
                                            <?php render_svg(SVG . '/icon-single-bano.svg'); ?>
                                        </div>
                                        <?php echo esc_html($bathroom_amount)?> <?php echo ($bathroom_amount <= 1) ? 'Baño' : 'Baños'; ?>
                                    </li>
                                <?php endif; ?>
                                <?php if($age_text): ?>
                                    <li>
                                        <div class="icon">
                                            <?php render_svg(SVG . '/icon-single-status.svg'); ?>
                                        </div>
                                        <?php echo esc_html($age_text);?>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <?php if($concepto): ?>
                            <div class="item">
                                <h2>Concepto</h2>
                                <p>
                                    <?php echo esc_html($concepto); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if($description_text):?>
                            <div class="item description">
                                <h2>Descripción</h2>
                                <?php echo $description_text; ?>
                            </div>
                        <?php endif; ?>
                        <?php if($servicios_only): ?>
                            <div class="item item-list">
                                <h2>Servicios</h2>
                                <?php if($servicios_only): ?>
                                <ul>
                                    <?php foreach($servicios_only as $tag): ?>
                                    <li><?php echo esc_html($tag['name']);  ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if($servicios_ambientes): ?>
                            <div class="item item-list">
                                <h2>Ambientes</h2>
                                <ul>
                                    <?php foreach($servicios_ambientes as $tag): ?>
                                    <li><?php echo esc_html($tag['name']);  ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?php if($servicios_adicionales): ?>
                            <div class="item item-list">
                                <h2>Adicionales</h2>
                                <ul>
                                    <?php foreach($servicios_adicionales as $tag): ?>
                                    <li><?php echo esc_html($tag['name']);  ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <div class="item location">
                            <h2>Ubicación</h2>
                            <p><?php echo esc_html($address);?>, <?php echo esc_html($location);?></p>
                            <div class="mapViewProperty">
                                <div id="mapviewproperty" class="mapViewSingle" data-lat="<?php echo esc_html($lat);?>" data-long="<?php echo esc_html($long);?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <aside class="col-sm-5 col-12" id="containerFormRequest">
                <form id="singlePropertyForm" data-origen="Form Propiedad" data-property="<?php echo $current_id?>" data-tags="Consulta de propiedad" data-event="form_propiedad">
                    <div class="content p-relative">
                        <?php
                            $valuePrice =  $total_price;
                            $valuePriceAlquiler =  $total_price_alquiler;
                            $operationTypeForm = $operation_type;
                            if($count_prices !== 1){
                                $operationTypeForm = 'Venta y Alquiler';
                            }
                        ?>
                        <input type="hidden" value="<?php echo esc_url(home_url( add_query_arg( null, null ) )); ?>" id="propertyName">
                        <input type="hidden" value="<?php echo esc_html($type_property); ?>"  id="propertyType">
                        <input type="hidden" value="<?php echo esc_html($operationTypeForm);?>" id="propertyOperation">
                        <input type="hidden" value="<?php echo esc_html($address);?>" id="propertyAddress">
                        <input type="hidden"  value="<?php echo esc_html($property['location']['name']);?>" id="propertyBarrio">
                        <input type="hidden"  value="<?php echo esc_html($valuePrice);?>" id="propertyPrice">
                        <input type="hidden"  value="<?php echo esc_html($valuePriceAlquiler);?>" id="propertyPriceAlquiler">
                        <input type="hidden"  value="<?php echo esc_html($total_surface);?>" id="propertyMetros">
                        <button type="button" class="d-none-sm" id="closeFormRequest">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
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
                    </div>
                </form>
            </aside>
            <div class="button-request d-none-sm">
                <button type="button" class="w-100 btn btn-black" id="openFormRequest">Consultar</button>
            </div>
        </div>
    </div>
    <div class="related-property">
        <h2>Propiedades similares</h2>
        <div class="container">
            <div class="row">
                <?php foreach ($property_similar_filter ?? [] as $property): ?>
                <?php
                    $name = $property['name'] ?? '';
                    $image = $property['photos'][0]['image'];
                    $location = get_full_location($property['location']['full_location']);
                    $currency_price = $property['operations'][0]['prices'][0]['currency'];
                    $price = $property['operations'][0]['prices'][0]['price'];
                    $total_price = render_price_format($price, $currency_price);
                    $operation_type = $property['operations'][0]['operation_type'];
                    $type_property = $property['type']['name'];
                    $address = $property['address'];
                    $total_surface = $property['total_surface'];
                    $bathroom_amount = $property['bathroom_amount'];
                    $permalink = return_url() . '/propiedad/' . slugify($address) . '/' . $property['id'];
                ?>
                <div class="<?php echo $class_property_similar;?>">
                    <a class="card-property" href="<?php echo $permalink?>"
                        title="Alquiler / Departamento -  170.000 USD  - Charcas al 2500 - Barrio Norte, Capital Federal"
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
        </div>
    </div>
</main>
<?php get_footer(); ?>