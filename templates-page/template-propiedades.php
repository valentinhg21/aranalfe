<?php 

/**

 * Template Name: Propiedades

 */

?>

<?php get_header(); ?>

<?php 

// QUERY VARS SLUG

$page = max(1, get_query_var('paged') ?: 1);
$operacion_slug = get_query_var('operacion');
$tipo_slug = get_query_var('tipo');
$ubicacion_slug = get_query_var('ubicacion');
$ubicacion_localidad_slug = get_query_var('localidad');
$antiguedad_slug = get_query_var('antiguedad');
$ambientes_slug = isset($_GET['ambientes']) ? sanitize_text_field($_GET['ambientes']) : '';






// VALIDACION DE LOCALIDADES Y UBICACION Y OPERACION
$propertys_validas = array_map(

    fn($type) => sanitize_title($type['type']),

    get_only_property_types()

);
$ubicaciones_validas = array_map(

    fn($loc) => sanitize_title($loc['parent_name']),

    get_only_parent_locations()

);
$ubicacion_localidad__validas = array_map(

    fn($localidad) => sanitize_title($localidad['location_name']),

    get_only_locations()

);









// SE GENERA EL SLUG

$operation_type_id = $operacion_slug === 'ventas' ? 1 : ($operacion_slug === 'alquiler' ? 2 : null);

$array_tipo_slug = split_slug_by_valid_terms($tipo_slug, $propertys_validas);

$array_ubicacion_slug = split_slug_by_valid_terms($ubicacion_slug, $ubicaciones_validas);

$array_ubicacion_localidad_slug = split_slug_by_valid_terms($ubicacion_localidad_slug, $ubicacion_localidad__validas);





$ids = get_tokko_ids_from_slugs(

    $array_tipo_slug, 

    $array_ubicacion_slug, 

    $array_ubicacion_localidad_slug 

);

$filtro_antiguedad = [];

if ($antiguedad_slug) {
    $slugs = explode('-', $antiguedad_slug);

    if (in_array('terminado', $slugs) && in_array('en_construccion', $slugs)) {
        $filtro_antiguedad = ['age', '>=', -1];
    } elseif (in_array('terminado', $slugs)) {
        $filtro_antiguedad = ['age', '>=', 0];
    } elseif (in_array('en_construccion', $slugs)) {
        $filtro_antiguedad = ['age', '=', -1];
    }
}

$ambientes_filtro = [];

if($ambientes_slug){
    if($ambientes_slug === '4'){
        $ambientes_filtro = ["room_amount", ">", "3"];
    }else{
        $ambientes_filtro = ["room_amount", "=", $ambientes_slug];
    }
    
}



$params = [

    'data' => [

        'current_localization_id' => $ids['location_localidad_id'], // ID de ciudad/localidad

        'current_localization_type' => 'division', // "country", "province", "city", "zone"

        'price_from' => 1,

        'price_to' => 9999999,

        'operation_types' => [$operation_type_id], // 1 = Venta

        'property_types' => $ids['property_type_id'],  // 2 = Departamento (por ejemplo)

        'currency' => 'ANY',

        'filters' => [$filtro_antiguedad, $ambientes_filtro] // filtros extra si querés

    ]

];



$params_2 = [

    'data' => [

        'current_localization_id' => $ids['location_localidad_id'], // ID de ciudad/localidad

        'current_localization_type' => 'division', // "country", "province", "city", "zone"

        'price_from' => 1,

        'price_to' => 9999999,

        'operation_types' => [$operation_type_id],// 1 = Venta

        'property_types' => [1,2,3,4,5,6,7,8],  // 2 = Departamento (por ejemplo)

        'currency' => 'ANY',

        'filters' => [$filtro_antiguedad, $ambientes_filtro] // filtros extra si querés

    ]

];



// BUSCA LAS PROPIEDADES EN BASO DE FILTROS

$offset = ($page - 1) * 12;

$current_url = home_url($_SERVER['REQUEST_URI']);



$propertys = get_all_property_by_filter($params, 12, $offset);

$total_count = $propertys['meta']['total_count'];





$slugs = array_map(function($str) {

    return ucwords(str_replace('-', ' ', $str));

}, $array_ubicacion_localidad_slug);



$slugs_tipo = array_map(function($str) {

    return ucwords(str_replace('-', ' ', $str));

}, $array_tipo_slug);



$texto_localidades = implode(', ', $slugs);

$texto_tipo = implode(', ', $slugs_tipo);

$texto_tipo_operacion = ($operacion_slug === 'alquilar') ? 'alquiler' : $operacion_slug;



// FILTROS VENTA Y ALQUILER



$all_propertys = get_all_property_by_filter($params_2, 1000, 0);

$filter_data = get_create_filter_data($all_propertys['objects']);





?>

<main class="properties" data-id="" data-page="<?php echo $page;?>" data-current="<?php echo $offset;?>"
    data-total="<?php echo $total_count;?>">

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-3  col-sm-3 col-12">

                <aside class="block-filters">

                    <?php if($total_count === 1): ?>

                    <p class="found">Encontraste <strong><?php echo $total_count;?> propiedad</strong></p>

                    <?php else: ?>

                    <p class="found">Encontraste <strong><?php echo $total_count;?> propiedades</strong></p>

                    <?php endif; ?>

                    <button type="button" class="close-filter d-none-sm">

                        <i class="fa-solid fa-xmark"></i>

                    </button>

                    <div class="filters-container">

                        <?php set_query_var('filtros', $filter_data); ?>

                        <?php get_template_part('/template-parts/content', 'filtro');?>

                    </div>

                </aside>

            </div>

            <div class="col-lg-9 col-sm-9 col-12">

                <section class="block-order-map">

                    <h1><?php echo $texto_tipo;?> en <?php echo $texto_tipo_operacion;?> en
                        <?php echo $texto_localidades;?></h1>

                    <div class="options">

                        <div class="select-order">

                            <div class="select-container select-simple-list d-flex-md d-none">

                                <div class="field-container-input__icon">

                                    <?php render_svg(SVG . '/icon-filter.svg') ?>

                                    <?php render_svg(SVG . '/icon-arrow-filter.svg') ?>

                                </div>

                                <input type="text" readonly value="Ordenar por: Precio más bajo" id="input-order"
                                    data-type="input-select">

                                <div class="list-select">

                                    <ul>

                                        <li class="options-list-select">

                                            <p>Ordenar por: Más reciente</p>

                                        </li>

                                        <li class="options-list-select">

                                            <p>Ordenar por: Precio más bajo</p>

                                        </li>

                                        <li class="options-list-select">

                                            <p>Ordenar por: Precio más alto</p>

                                        </li>

                                    </ul>

                                </div>

                            </div>

                            <div class="select-container select-simple-list d-none-md select-order-mobile">

                                <div class="field-container-input__icon">

                                    <?php render_svg(SVG . '/icon-double-arrow.svg') ?>

                                </div>



                                <input type="text" readonly value="Ordenar por: Precio más bajo" id="input-order"
                                    data-type="input-select">

                                <div class="list-select">

                                    <ul>

                                        <li class="options-list-select">

                                            <p>Más recientes</p>

                                        </li>

                                        <li class="options-list-select">

                                            <p>Precio más bajo</p>

                                        </li>

                                        <li class="options-list-select">

                                            <p>Precio más alto</p>

                                        </li>

                                    </ul>

                                </div>

                            </div>

                            <button type="button" class="btn btn-white-black btn-icon d-none-sm">

                                <?php render_svg(SVG . '/icon-filter-mobile.svg') ?>

                            </button>

                            <button type="button" class="btn btn-white-black btn-icon active">

                                <?php render_svg(SVG . '/icon-list.svg') ?>

                            </button>

                            <button type="button" class="btn btn-white-black btn-icon">

                                <?php render_svg(SVG . '/icon-map.svg') ?>

                            </button>

                        </div>

                    </div>

                </section>

                <section class="block-results">

                    <div class="row">

                        <?php foreach ($propertys['objects'] ?? [] as $property): ?>

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

                            ?>

                        <div class="col-xxl-3 col-md-4 col-sm-6 col-12">

                            <a class="card-property" href="#"
                                title="Alquiler / Departamento -  170.000 USD  - Charcas al 2500 - Barrio Norte, Capital Federal"
                                href="_blank">

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



                    <?php echo render_pagination($current_url, $total_count); ?>



                </section>

            </div>
            <style>
            #map {
                height: 100vh;
                width: 100%;
            }
            </style>
            <div class="col-12 d-none" id="map"></div>
            <script>
            function initMap() {
                const ubicacion = {
                    lat: -34.6037,
                    lng: -58.3816
                }; // Buenos Aires
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 12,
                    center: ubicacion,
                });
                new google.maps.Marker({
                    position: ubicacion,
                    map: map
                });
            }
            </script>
            <script async defer
                src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmtGfkkCtT7Pl4qIez45BsQhUpAxb0QLY&callback=initMap">
            </script>
        </div>

    </div>

</main>

<?php get_footer(); ?>