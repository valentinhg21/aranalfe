<?php 
/**
 * Template Name: Propiedades
 */
?>



<?php get_header(); ?>
<?php 
$current_url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
// QUERY VARS SLUG
$page = max(1, get_query_var('paged') ?: 1);
// OPERACION
$operacion_slug = isset($_GET['operacion']) ? ($_GET['operacion']) : [1,2];
// Localidad
if (isset($_GET['localidad'])) {
    // Viene como array: localidad[]=2&localidad[]=4
    $ubicacion_localidad_slug = array_map('intval', (array)$_GET['localidad']);
    $division_localidad = 'division';
    $current_localization_id = $ubicacion_localidad_slug[0] ?? 0;

} else {
    $ubicacion_localidad_slug = 0;
    $division_localidad = 'country';
    $current_localization_id = 0;
}

// PROPIEDAD
if (isset($_GET['tipo'])) {
    $type_property_slug = array_map('intval', (array)$_GET['tipo']);
} else {
    $type_property_slug = range(1, 28);
}



// Antiguedad
$antiguedad_slug = $_GET['antiguedad'] ?? [];
$antiguedad_slug = is_array($antiguedad_slug) ? $antiguedad_slug : explode(',', $antiguedad_slug);
$antiguedad_slug = array_map('sanitize_text_field', $antiguedad_slug);
$filtro_antiguedad = [];



if ($antiguedad_slug) {
    if (in_array('terminado', $antiguedad_slug, true) && in_array('en-construccion', $antiguedad_slug, true)) {
        // Mostrar todo excepto "a estrenar"
        $filtro_antiguedad = [
            ['age', '>=', -1]
        ];
    } elseif (in_array('a-estrenar', $antiguedad_slug, true)) {
        $filtro_antiguedad = [
            ['age', '=', 0]
        ];
    } elseif (in_array('en-construccion', $antiguedad_slug, true)) {
        $filtro_antiguedad = [
            ['age', '=', -1]
        ];
    } elseif (in_array('terminado', $antiguedad_slug, true)) {
        $filtro_antiguedad = [
            ['age', '>=', 1]
        ];
    }
}



// Ambientes
$ambientes_slug = $_GET['ambientes'] ?? [];
$ambientes_slug = is_array($ambientes_slug) ? $ambientes_slug : explode(',', $ambientes_slug);
$ambientes_slug = array_map('sanitize_text_field', $ambientes_slug);
$ambientes_filtro = [];
if (!empty($ambientes_slug)) {
    foreach ($ambientes_slug as $valor) {
        if ($valor === '4') {
            $ambientes_filtro[] = ['room_amount', '>', 3];
        } else {
            $ambientes_filtro[] = ['room_amount', '=', intval($valor)];
        }
    }
}
$dormitorios_slug = sanitize_text_field($_GET['dormitorio'] ?? '');
$dormitorios_filtro = [];
if ($dormitorios_slug !== '') {
    if ($dormitorios_slug === '4') {
        $dormitorios_filtro[] = ['suite_amount', '>', 3];
    } elseif (is_numeric($dormitorios_slug)) {
        $dormitorios_filtro[] = ['suite_amount', '=', (int)$dormitorios_slug];
    }
}



// FILTRO PRECIO
$moneda_slug = isset($_GET['moneda']) ? sanitize_text_field($_GET['moneda']) : 'USD';
$precio_min_slug = ($val = preg_replace('/[^\d]/', '', $_GET['precio_min'] ?? '')) !== '' ? (int) $val : 1;
$precio_max_slug = ($val = preg_replace('/[^\d]/', '', $_GET['precio_max'] ?? '')) !== '' ? (int) $val : 999999999;
// FILTROS SUPERFICIE
$sup_min_slug = ($val_sup = preg_replace('/[^\d]/', '', $_GET['sup_min'] ?? '')) !== '' ? (int) $val_sup : 1;
$sup_max_slug = ($val_sup = preg_replace('/[^\d]/', '', $_GET['sup_max'] ?? '')) !== '' ? (int) $val_sup : 999999999;
$superficie_filtro = [];
if($sup_min_slug){
    $superficie_filtro = ["total_surface", ">", $sup_min_slug];
}
if($sup_max_slug){
    $superficie_filtro = ["total_surface", "<", $sup_min_slug];
}
if($sup_min_slug && $sup_max_slug){
    $superficie_filtro = [["total_surface", ">", $sup_min_slug], ["total_surface", "<", $sup_max_slug]];
}
//FILTRO 
$order_by_slug = isset($_GET['order_by']) ? sanitize_text_field($_GET['order_by']) : 'created_at';
$order_slug = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';
if($order_by_slug == 'created_date'){
    $order_by_slug = 'created_at';
}
$operation_type_id = $operacion_slug === '1' ? [1] : ($operacion_slug === '2' ? [2] : [1,2]);
$servicios_filtro = [];
$offset = ($page - 1) * 12;
$servicios_activa = [];
$servicios_otros_activa = [];
// Capturar y sanitizar servicios
if (isset($_GET['servicios'])) {
    $servicios_activa = is_array($_GET['servicios']) 
        ? array_map('sanitize_text_field', $_GET['servicios']) 
        : [sanitize_text_field($_GET['servicios'])];
}
// Capturar y sanitizar otros servicios
if (isset($_GET['otros'])) {
    $servicios_otros_activa = is_array($_GET['otros']) 
        ? array_map('sanitize_text_field', $_GET['otros']) 
        : [sanitize_text_field($_GET['otros'])];
}
$servicios_filtro = array_unique(array_merge($servicios_activa, $servicios_otros_activa));
// ORIGINAL
$params = [
    'data' => [
        'current_localization_id' => $ubicacion_localidad_slug, // ID de ciudad/localidad
        'current_localization_type' => $division_localidad, // "country", "province", "city", "zone"
        'price_from' => $precio_min_slug,
        'price_to' => $precio_max_slug,
        'operation_types' => $operation_type_id, // 1 = Venta
        'property_types' => $type_property_slug  ,  // 2 = Departamento (por ejemplo)
        'currency' => in_array($moneda_slug, ['USD', 'ARS']) ? $moneda_slug : 'ANY',
        'filters' => array_values(array_merge(
            $filtro_antiguedad ?? [],
            $ambientes_filtro ?? [],
            $dormitorios_filtro ?? [],
            $superficie_filtro ?? []
        )),
        'with_tags' => $servicios_filtro
    ]
];
// FETCH API 
$propertys = get_all_property_by_filter($params, 12, $offset, $order_by_slug, $order_slug);
$total_count = $propertys['meta']['total_count'] ?? 0;
$params_2 = [
    'data' => [
        // Localization
        'current_localization_type' => $division_localidad, // 'country', 'state', 'division'
        'current_localization_id' => $ubicacion_localidad_slug,
        // Rango de precios6791331v
        'price_from' => intval($precio_min_slug),
        'price_to' => intval($precio_max_slug),
        // Tipo de operación
        'operation_types' => array_map('intval', (array) $operation_type_id), // Asegura array
        // Tipos de propiedad
        'property_types' => array_map('intval', (array) $type_property_slug),
        // Moneda
        'currency' => in_array($moneda_slug, ['USD', 'ARS']) ? $moneda_slug : 'ANY',
        // Filtros avanzados
        'filters' => array_values(array_merge(
            $filtro_antiguedad ?? [],
            $ambientes_filtro ?? [],
            $dormitorios_filtro ?? [],
            $superficie_filtro ?? []
        )),
        // Tags
        'with_tags' => array_map('intval', (array) $servicios_filtro)
    ]
];
$summary_data = get_search_summary($params_2) ?? [];
$dataFilter = (
    isset($summary_data['objects']) && is_array($summary_data['objects'])
) ? $summary_data['objects'] : [];
$filter_data = get_create_filter_data($dataFilter);
$filter_tag = get_create_filter_tag($dataFilter);
$search_location = isset($summary_data['objects']['locations']) ? $summary_data['objects']['locations'] : [];
$barrios = get_field('barrios', 'options') ?? [];


?>


<script>
  let locations_data = <?php echo json_encode(array_map(function($l) { return $l; }, $search_location)); ?>;
  let locations_theme = <?php echo json_encode(array_map(function($l) { return $l; }, $barrios)); ?>;
</script>

<main class="properties" data-id="" data-page="<?php echo $page;?>" data-current="<?php echo $offset;?>"
    data-total="<?php echo $total_count;?>" data-url="<?php echo ((is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'])?>">
    <div class="container-fluid">
        <style>
            .data-hide{

                *{

                    color: #000 !important;

                }

            }

        </style>
        <div class=" d-none data-hide">

                <?php if (!empty($params_2)): ?>

                    <table border="1" cellpadding="5" cellspacing="0">

                        <thead>

                            <tr>

                                <th>Clave</th>

                                <th>Valor</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($propertys as $key => $value): ?>

                                <tr>

                                    <td><?= print_r($key) ?></td>

                                    <td>

                                        <?php if (is_array($value)): ?>

                                            <pre><?= print_r(print_r($value, true)) ?></pre>

                                        <?php else: ?>

                                            <?= print_r($value) ?>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                <?php else: ?>

                    <p>No hay datos.</p>

                <?php endif; ?>

        </div>
        <div class="row">
            <div class="col-lg-3 col-sm-4 col-12 col-filtros">
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
                        <form class="form-location d-flex-md d-none">
                            <div class="input__container">
                                <input type="text" class="location-input" id="location-input" placeholder="Seleccionar barrios">
                                <button type="submit" class="btn btn-search search-location" id="search-location">
                                    <?php render_svg(SVG . '/icon-search.svg'); ?>
                                </button>
                            </div>
                        </form>
                        <?php echo render_filters_apply($ubicacion_localidad_slug, $barrios); ?>
                        
                        <?php set_query_var('filtros', $filter_data); ?>
                        <?php set_query_var('filtros_tag', $filter_tag); ?>
                        <?php get_template_part('/template-parts/content', 'filtro');?>
                    </div>
                </aside>
            </div>
            <div class="col-lg-9 col-sm-8 col-12">
                <section class="d-none-md form-location-mobile">
                    <form class="form-location">
                            <div class="input__container">
                                <input class="location-input" type="text" id="location-input" placeholder="Seleccionar barrios">
                                <button type="submit" class="btn btn-search search-location" id="search-location">
                                    <?php render_svg(SVG . '/icon-search.svg'); ?>
                                </button>
                            </div>
                    </form>
                    <?php echo render_filters_apply($ubicacion_localidad_slug, $barrios); ?>
                </section>

                <section class="block-order-map">
                    <?php if($total_count != 0): ?>
                        <?php render_property_title($_GET, $ubicacion_localidad_slug, $barrios, $type_property_slug, $dataFilter);?>
                        <?php else: ?>
                            <h1></h1>
                    <?php endif; ?>
                    <div class="options">
                        <div class="select-order">
                            <?php 
                            $order_by = $_GET['order_by'] ?? '';
                            $order = $_GET['order'] ?? '';
                            switch ("$order_by-$order"){
                                case 'price-DESC':
                                    $texto_select = 'Precio más alto';
                                    break;
                                case 'price-ASC':
                                    $texto_select = 'Precio más bajo';
                                    break;
                                case 'created_at-DESC':
                                    $texto_select = 'Más reciente';
                                    break;
                                default:
                                    $texto_select = 'Más reciente';
                            }
                            ?>

                            <div class="select-container select-simple-list d-flex-md d-none">
                                <div class="field-container-input__icon">
                                    <?php render_svg(SVG . '/icon-filter.svg') ?>
                                    <?php render_svg(SVG . '/icon-arrow-filter.svg') ?>
                                </div>
                                <input type="text" readonly value="Ordenar por: <?php echo $texto_select?>" id="input-order"
                                    data-type="input-select">
                                <div class="list-select">
                                    <ul>
                                        <?php render_order_options($order_by, $order); ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="select-container select-simple-list d-none-md select-order-mobile">
                                <div class="field-container-input__icon">
                                    <?php render_svg(SVG . '/icon-double-arrow.svg') ?>
                                </div>
                                <input type="text" readonly value="Ordenar por: <?php echo $texto_select?>" id="input-order-mobile"
                                    data-type="input-select">
                                <div class="list-select">
                                    <ul>
                                        <?php render_order_options($order_by, $order); ?>
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn btn-white-black btn-icon d-none-sm btn-open-filter">
                                <?php render_svg(SVG . '/icon-filter-mobile.svg') ?>
                            </button>
                            <button type="button" class="btn btn-white-black btn-icon active btn-close-map d-flex-sm d-none">
                                <?php render_svg(SVG . '/icon-list.svg') ?>
                            </button>
                            <button type="button" class="btn btn-white-black btn-icon btn-open-map d-flex-sm d-none">
                                <?php render_svg(SVG . '/icon-map.svg') ?>
                            </button>
                            <div class="toggle-map-btn d-none-sm">
                                <button type="button" class="btn btn-white-black btn-icon btn-toggle-map">
                                    <?php render_svg(SVG . '/icon-map.svg') ?>
                                    <?php render_svg(SVG . '/icon-list.svg') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="block-results">
                        <?php if($total_count != 0): ?>
                            <div class="row row-results">
                                <?php foreach ($propertys['objects'] ?? [] as $property): ?>
                                <?php 
                                    $name = $property['name'] ?? '';
                                    $image = $property['photos'][0]['image'] ?? '';
                                    $location = get_full_location($property['location']['full_location']);
                                    $operation_order = $property['operations'];

                                    usort($operation_order, function ($a, $b) {
                                        return $a['operation_id'] <=> $b['operation_id'];
                                    });

                                   
                           
                                    $currency_price = $operation_order[0]['prices'][0]['currency'];
                                    if(count($property['operations']) === 2){
                                   
                                        if($operacion_slug === '2'){
                                            $price = $operation_order[1]['prices'][0]['price'];
                                            $operation_type = $operation_order[1]['operation_type'];

                                        }else{
                                            
                                            $price = $operation_order[0]['prices'][0]['price'];
                                            $operation_type = $operation_order[0]['operation_type'];
                                            
                                        }
                                    }else{
                                        $price = $operation_order[0]['prices'][0]['price'];
                                        $operation_type = $operation_order[0]['operation_type'];
                                    }
                                    $total_price = render_price_format($price, $currency_price);
                                    $type_property = $property['type']['name'];
                                    $address = $property['address'];
                                    $total_surface = $property['total_surface'];
                                    $bathroom_amount = $property['bathroom_amount'];
                                    $permalink = return_url() . '/propiedad/' . slugify($address) . '/' . $property['id'];
                                    $created_at = $property['created_at'];
                                ?>
                                <div class="col-xxl-3 col-md-4 col-xs-6 col-12"  data-date = "<?php echo $created_at;?>">
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
                            <?php else: ?>
                                <p class="error-msg">No se encontraron resultados</p>
                                <div class="button__container w-100 justify-center align-items-center mt-2">
                                    <a href="<?php echo return_url();?>/propiedades/" class="btn btn-black">
                                        Volver atras
                                    </a>
                                </div>
                        <?php endif; ?>
                    <?php  echo render_pagination($current_url, $total_count); ?>
                    <section class="mapView" id="mapview"></section>
                    <script>
                        const propertiesData = <?= json_encode(array_map(function($p) use ($operacion_slug) {
                        $operations = $p['operations'] ?? [];
                        $price = null;
                        $operation_type = null;
                        if (count($operations) === 2) {
                            if ($operacion_slug === '2') {
                                $price = $operations[1]['prices'][0]['price'] ?? null;
                                $operation_type = $operations[1]['operation_type'] ?? null;
                            } else {
                                $price = $operations[0]['prices'][0]['price'] ?? null;
                                $operation_type = $operations[0]['operation_type'] ?? null;
                            }

                        } elseif (count($operations) > 0) {
                            $price = $operations[0]['prices'][0]['price'] ?? null;
                            $operation_type = $operations[0]['operation_type'] ?? null;
                        }
                        return [
                            'name' => $p['name'] ?? '',
                            'lat' => $p['geo_lat'] ?? null,
                            'lng' => $p['geo_long'] ?? null,
                            'operation_type' => $operation_type,
                            'price' => render_price_format($price, $operations[0]['prices'][0]['currency'] ?? 'USD'),
                            'image' => $p['photos'][0]['image'] ?? null,
                            'type' => $p['type']['name'] ?? '',
                            'address' => $p['address'] ?? '',
                            'location' => get_full_location($p['location']['full_location'] ?? '') ?? '',
                            'total_surface'=> $p['total_surface'] ?? null,
                            'bathroom_amount'=> $p['bathroom_amount'] ?? null,
                            'permalink' => return_url() . '/propiedad/' . slugify($p['address'] ?? '') . '/' . ($p['id'] ?? '')
                        ];

                    }, $propertys['objects'] ?? [])); ?>;
                    </script>
                </section>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>