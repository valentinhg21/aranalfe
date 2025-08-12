<?php
/**
 * Template Name: Emprendimientos
 */

get_header();

$developments = get_developments(); // Trae todos los desarrollos

$file_path = get_template_directory() . '/tokko-api/data/developments.json';
if (!file_exists($file_path)) return [];
$json = file_get_contents($file_path);
$data = json_decode($json, true);
if (empty($data) || !is_array($data)) return [];


$developments = $data['objects'];

// Agrupar por estado
$grouped = ['ver-todas' => $developments];
foreach ($developments as $dev) {
    $status = $dev['construction_status'] ?? 'Sin estado';
    $key = sanitize_title($status);
    $grouped[$key][] = $dev;
}

// Mapeo de títulos para estados
$status_titles = [
    'ver-todas' => 'Ver todas',
    '3'         => 'En pozo',
    '4'         => 'En construcción',
    '6'         => 'Terminado',
    'sin-estado'=> 'Sin estado'
];
?>

<main class="template-emprendimientos">
    <section class="section-1">
        <div class="container">
            <div class="d-flex flex-row-md flex-column align-items-start justify-between">
                <div>
                    <h1 class="h1 d-flex flex-column">
                        <strong class="strong">Los emprendimientos más excepcionales</strong>
                        en Buenos Aires y Punta del Este
                    </h1>
                </div>

                <!-- TABS -->
                <div class="tabs tabs-nav">
                    <div class="tab-buttons d-none d-flex-md align-items-center justify-between">
                        <?php foreach ($grouped as $key => $items): 
                            $keyTitle = $status_titles[$key] ?? ucfirst(str_replace('-', ' ', $key));
                        ?>
                            <div>
                                <button class="tab-btn<?php echo $key === 'ver-todas' ? ' active' : ''; ?>"
                                        data-tab="<?php echo esc_attr($key); ?>">
                                    <?php echo esc_html($keyTitle); ?>
                                </button>
                            </div>
                            <div class="hr"></div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Mobile: dropdown -->
                <select class="tab-dropdown d-none-md d-block">
                    <?php foreach ($grouped as $key => $items): 
                        $keyTitle = $status_titles[$key] ?? ucfirst(str_replace('-', ' ', $key));
                    ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php echo $key === 'ver-todas' ? ' selected' : ''; ?>>
                            <?php echo esc_html($keyTitle); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- CONTENIDOS -->
            <div class="tabs-content">
                <?php foreach ($grouped as $key => $items): ?>
                    <div class="tab-content-group<?php echo $key === 'ver-todas' ? ' active' : ''; ?>"
                         data-tab-content="<?php echo esc_attr($key); ?>">
                        <div class="row">
                            <?php foreach ($items as $item):
                                $id = $item['id'] ?? '';
                                $name = $item['name'] ?? '';
                                $image = $item['photos'][0]['image'] ?? '';
                                $location = get_full_location($item['location']['full_location'] ?? '') ?? '';
                                $permalink = (function_exists('return_url') ? return_url() : home_url()) . '/emprendimiento/' . sanitize_title($name);
                                $status = get_construction_status($item['construction_status'] ?? '');
                                
                                $post = get_page_by_path(sanitize_title($name), OBJECT, 'emprendimiento');
                                $object_position = 'center';
                                if ($post) {
                                    $pos = get_field('object_position', $post->ID); // campo ACF que contenga posición
                                    if ($pos) {
                                        $object_position = $pos;
                                    }
                                }
                            ?>
                                <div class="col-md-4 col-12 mb-md-4">
                                    <a href="<?php echo esc_url($permalink); ?>" class="card-feature" data-id="<?php echo esc_attr($id); ?>" title="Emprendimiento - <?php echo esc_attr($name); ?>">
                                        <div class="image">
                                            <img <?php echo get_object_position($object_position); ?> src="<?php echo esc_url($image); ?>" alt="En la imagen se muestra el emprendimiento - <?php echo esc_attr($name); ?>" width="100%" height="100%" loading="lazy" >
                                        </div>
                                        <div class="content">
                                            <span class="status"><?php echo esc_html($status); ?></span>
                                            <h3 class="title"><?php echo esc_html($name); ?></h3>
                                            <p class="location"><?php echo esc_html($location); ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<!-- SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.tab-btn');
        const dropdown = document.querySelector('.tab-dropdown');
        const contents = document.querySelectorAll('.tab-content-group');

        function showTab(target) {
            buttons.forEach(b => b.classList.remove('active'));
            document.querySelector(`.tab-btn[data-tab="${target}"]`)?.classList.add('active');

            contents.forEach(c => {
                c.classList.toggle('active', c.getAttribute('data-tab-content') === target);
            });

            if (dropdown.value !== target) dropdown.value = target;
        }

        buttons.forEach(btn => {
            btn.addEventListener('click', function () {
                const target = this.getAttribute('data-tab');
                showTab(target);
            });
        });

        dropdown.addEventListener('change', function () {
            showTab(this.value);
        });
    });
</script>

<?php get_footer(); ?>