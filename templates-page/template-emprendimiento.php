<?php


require get_template_directory() . '/tokko/core.php';

$idEmprendimiento = get_field('id');
$development = getDevelopment($idEmprendimiento); ?>

<?php get_header(); ?>
<main class="template-emprendimiento ">
    <section class="section-1 p-relative d-flex align-items-end justify-end <?php echo $development->id; ?>">
        <img src="<?php echo $development->photos[0]->image; ?>" class="img-cover" alt="">
        <div class="container p-relative">
            <h1><?php the_title(); ?></h1>
            <h2><?php echo $development->location->name; ?></h2>
            <button class="btn btn-stroke-white btn-icon d-flex align-items-center p-absolute d-none d-flex-md align-items-center btnModal">
                <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19"
                    fill="none">
                    <path d="M1.34424 6.99091H11.6284" stroke="white" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M15.3682 6.99091H19.1079" stroke="white" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M7.8877 17.5088V6.99091" stroke="white" stroke-width="1.5" />
                    <path d="M7.8877 4.36143V0.855469" stroke="white" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path
                        d="M14.9006 13.5646C13.0308 13.5646 11.1609 14.9865 11.1609 17.5088H8.82362C5.2978 17.5088 3.5349 17.5088 2.43957 16.4819C1.34424 15.4551 1.34424 13.8023 1.34424 10.4969V7.86739C1.34424 4.56194 1.34424 2.90922 2.43957 1.88234C3.5349 0.855469 5.2978 0.855469 8.82362 0.855469H11.6284C15.1542 0.855469 16.9172 0.855469 18.0124 1.88234C19.1078 2.90922 19.1078 4.56194 19.1078 7.86739V14.3036C19.1078 16.0738 17.5771 17.5088 15.689 17.5088"
                        stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Ver plano
            </button>
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
                    <p>Edificio</p>
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
                    <?php $estados = [
                        1 => 'En pozo',
                        2 => 'En construcción',
                        3 => 'Terminado'
                    ];

                    $id_estado = $development->construction_status;
                    $nombre_estado = $estados[$id_estado] ?? 'Desconocido'; ?>

                    <p><?php echo $development->construction_status ?></p>
                </div>
            </div>
            <div class="hr"></div>
            <div class="d-flex align-items-start dato">
                <svg xmlns="http://www.w3.org/2000/svg" width="31" height="34" viewBox="0 0 31 34" fill="none">
                    <path
                        d="M1.82422 31.7667H29.541M3.08407 1.53021H28.2812M4.34393 1.53021V31.7667M27.0213 1.53021V31.7667M10.6432 7.82948H13.1629M10.6432 12.8689H13.1629M10.6432 17.9083H13.1629M18.2023 7.82948H20.722M18.2023 12.8689H20.722M18.2023 17.9083H20.722M10.6432 31.7667V26.0974C10.6432 25.0542 11.4898 24.2076 12.533 24.2076H18.8323C19.8754 24.2076 20.722 25.0542 20.722 26.0974V31.7667"
                        stroke="#E40729" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div>
                    <h3>Disponibles</h3>
                    <?php
                    $data = json_encode([
                        "current_localization_id" => 0,
                        "current_localization_type" => "country",
                        "price_from" => 1,
                        "price_to" => 999999999999,
                        "operation_types" => [1, 2, 3],
                        "property_types" => range(1, 25),
                        "currency" => "ANY",
                        "filters" => [
                            ["development__id", "=", $development->id]
                        ]
                    ]);

                    $unidades = searchProperties(100, 0, 'id', 'DESC', $data);
                    //$cantidad_unidades = count($unidades)
                   $cantidad_disponibles = isset($unidades->objects) ? count($unidades->objects) : 0;
                    ?>
                    <p> <?php echo $cantidad_disponibles; ?> </p>
                </div>
            </div>
            <div class="hr"></div>
            <div class="d-flex align-items-start dato">
                <svg xmlns="http://www.w3.org/2000/svg" width="31" height="34" viewBox="0 0 31 34" fill="none">
                    <path
                        d="M1.82422 31.7667H29.541M3.08407 1.53021H28.2812M4.34393 1.53021V31.7667M27.0213 1.53021V31.7667M10.6432 7.82948H13.1629M10.6432 12.8689H13.1629M10.6432 17.9083H13.1629M18.2023 7.82948H20.722M18.2023 12.8689H20.722M18.2023 17.9083H20.722M10.6432 31.7667V26.0974C10.6432 25.0542 11.4898 24.2076 12.533 24.2076H18.8323C19.8754 24.2076 20.722 25.0542 20.722 26.0974V31.7667"
                        stroke="#E40729" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div>
                    <h3>Entrega</h3>
                    <?php 
                    $construction_date = $development->construction_date;
                                $fecha_construccion = date('d/m/Y', strtotime($construction_date));
                            $today = date('Y-m-d');
                            if ($construction_date > $today) {?>
                    <p><?php echo $fecha_construccion; ?></p>
                    <?php } else{ ?>
                            <div class="d-flex flex-column align-items-center">
                                <h3 class="poppins-bold">Entrega <br> Inmediata</h3>
                            </div>
                            
                        <?php } ?>
                </div>
            </div>

            <button class="btn btn-stroke-black btn-icon d-flex align-items-center p-absolute d-none-md d-flex align-items-center btn-modal">
                <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19"
                    fill="none">
                    <path d="M1.34424 6.99091H11.6284" stroke="black" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M15.3682 6.99091H19.1079" stroke="black" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path d="M7.8877 17.5088V6.99091" stroke="black" stroke-width="1.5" />
                    <path d="M7.8877 4.36143V0.855469" stroke="black" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    <path
                        d="M14.9006 13.5646C13.0308 13.5646 11.1609 14.9865 11.1609 17.5088H8.82362C5.2978 17.5088 3.5349 17.5088 2.43957 16.4819C1.34424 15.4551 1.34424 13.8023 1.34424 10.4969V7.86739C1.34424 4.56194 1.34424 2.90922 2.43957 1.88234C3.5349 0.855469 5.2978 0.855469 8.82362 0.855469H11.6284C15.1542 0.855469 16.9172 0.855469 18.0124 1.88234C19.1078 2.90922 19.1078 4.56194 19.1078 7.86739V14.3036C19.1078 16.0738 17.5771 17.5088 15.689 17.5088"
                        stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Ver plano
            </button>
        </div>
    </section>
    <section class="section-3">
        <div class="container d-flex flex-column flex-row-md align-items-start justify-between">
            <div class="cont-img">
                <img src="<?php echo $development->photos[1]->image; ?>" class="img-cover" alt="">
            </div>
            <div class="cont-info d-flex flex-column">
                <div class="info">
                    <h2>Concepto</h2>
                    <p><?php echo $development->publication_title; ?></p>
                </div>
                <div class="info">
                    <h2>Descripcion</h2>
                    <?php $descripcion = $development->description; ?>
                    <p><?php echo nl2br($descripcion); ?></p>
                </div>
                <div class="info">
                    <h2>Amenities</h2>
                    <ul>
                        <?php foreach ($development->tags as $tag) { ?>
                            <?php if ($tag->type == 3): ?>
                                <li>
                                    <?= $tag->name; ?>
                                </li>
                            <?php endif; ?>
                            <?php
                        } ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <?php if (isset($development->photos) && is_array($development->photos)) { ?>
        <section class="section-4">
            <div class="container d-flex justify-center">
                <div class="swiper swiper-galeria">
                    <div class="swiper-wrapper">

                        <?php foreach ($development->photos as $img) { ?>
                            <div class="swiper-slide">
                                <img src="<?php echo $img->image; ?>" class="img-cover" alt="">
                            </div>
                        <?php } ?>

                    </div>
                    <div class="swiper-button-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="51" height="51" viewBox="0 0 51 51" fill="none">
                            <path d="M33.7603 41.0615L18.1353 25.4365L33.7603 9.81152" stroke="white" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <div class="swiper-button-next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="51" height="51" viewBox="0 0 51 51" fill="none">
                            <path d="M17.2925 41.0615L32.9175 25.4365L17.2925 9.81152" stroke="white" stroke-width="3"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>
    <?php } ?>
    <section class="section-5">
        <div class="container">
            <div class="d-flex flex-row-md flex-column align-items-center justify-between">
            <h2>Unidades disponibles</h2>
            <?php
            $data = json_encode([
                "current_localization_id" => 0,
                "current_localization_type" => "country",
                "price_from" => 1,
                "price_to" => 999999999999,
                "operation_types" => [1, 2, 3],
                "property_types" => range(1, 25),
                "currency" => "ANY",
                "filters" => [
                    ["development__id", "=", $development->id]
                ]
            ]);

            $unidades = searchProperties(100, 0, 'id', 'DESC', $data);
            
            ?>
            <?php
// Agrupar unidades por cantidad de ambientes
$unidades_por_ambiente = [];

foreach ($unidades->objects as $unidad) {
    $ambientes = $unidad->room_amount ?? 0;
    if (!isset($unidades_por_ambiente[$ambientes])) {
        $unidades_por_ambiente[$ambientes] = [];
    }
    $unidades_por_ambiente[$ambientes][] = $unidad;
}

// Ordenamos los tabs por cantidad de ambientes
ksort($unidades_por_ambiente);
?>

<!-- TABS: encabezado -->
<!-- TABS: encabezado -->
<div class="tabs-nav d-none d-flex-md align-items-center justify-between">
    <?php $i = 0; foreach ($unidades_por_ambiente as $ambientes => $grupo): ?>
        <div class="<?= $i === 0 ? 'active' : '' ?>">
            <a href="#tab-<?= $ambientes ?>">
                <?= $ambientes === 0 ? 'Sin info' : $ambientes . ' ambiente' . ($ambientes > 1 ? 's' : '') ?>
            </a>
        </div>
        <div class="hr"></div>
    <?php $i++; endforeach; ?>
</div>

<!-- Dropdown para mobile -->
<div class="tabs-dropdown d-block d-none-md">
    <select id="tabs-select">
        <?php foreach ($unidades_por_ambiente as $ambientes => $grupo): ?>
            <option value="tab-<?= $ambientes ?>">
                <?= $ambientes === 0 ? 'Sin info' : $ambientes . ' ambiente' . ($ambientes > 1 ? 's' : '') ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

</div>
<!-- TABS: contenido -->
<div class="tabs-content">
    <?php $i = 1; foreach ($unidades_por_ambiente as $rooms => $grupo): ?>
        <div  id="tab-<?= $i ?>" class="tab <?= $i === 1 ? 'active' : '' ?>">
             <div class="tabla">
                <div class="tabla-header">
                    <div class="col">Piso</div>
                    <div class="col d-none d-blok-md">Ambientes</div>
                    <div class="col d-none-md d-block">Amb.</div>
                    <div class="col d-none d-block-md">Sup. Interior</div>
                    <div class="col">Sup. Total</div>
                    <div class="col">Precio</div>
                </div>
                <?php foreach ($grupo as $unidad): ?>
                    <div class="tabla-row">
                        <div class="col"><?= $unidad->floor ?? '—' ?></div>
                        <div class="col "><?= $unidad->room_amount ?? '—' ?></div>
                        <div class="col d-none d-block-md"><?= $unidad->roofed_surface ?? '—' ?> m²</div>
                        <div class="col"><?= $unidad->total_surface ?? '—' ?> m²</div>
                        <div class="col">
                            <?php
                                if (!empty($unidad->web_price)) {
                                    echo '$ ' . number_format($unidad->web_price, 0, ',', '.');
                                } else {
                                    echo 'Consultar';
                                }
                            ?>
                        </div>
                        <div class="col d-none d-block-md">
                            <a href="" class="btn btn-black">Ver</a>
                        </div>
                        <div class="col d-none-md d-block">
                            <a href="" class="btn btn-black">+</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php $i++; endforeach; ?>
</div>
        </div>
    </section>
    <section class="section-6">
        <div class="container">
            <h2>Ubicación</h2>
            <p><?php echo $development->address ;?></p>

        </div>
    </section>
    <section class="section-7">
        <div class="container d-flex flex-row-md flex-column justify-between align-items-start">
            <div class="cont-texto">
                <h2><strong>¿Querés más detalles </strong> <br> de este emprendimiento?</h2>
                <p>Para más información completá el formulario de consultas</p>
            </div>
            <div class="cont-form">
                <form action="" class="w-100 form-contacto">
                    <div class="d-flex flex-column align-items-start">
                        <label for="">Nombre y apellido</label>
                        <input type="text">
                    </div>
                    <div class="d-flex flex-column align-items-start">
                        <label for="">teléfono</label>
                        <input type="tel">
                    </div>
                    <div class="d-flex flex-column align-items-start">
                        <label for="">correo electrónico</label>
                        <input type="email">
                    </div>
                    <div class="d-flex flex-column align-items-start"> 
                        <label for="">Mensaje</label>
                        <textarea name="" id=""></textarea>
                    </div>
                    <button type="submit" class="btn btn-black">Enviar</button>
                </form>
            </div>
        </div>
    </section>
    <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <?php if (have_rows('contacto')): ?>
                        <?php while (have_rows('contacto')):
                            the_row(); ?>
        <img src="<?php echo esc_url(get_sub_field('plano')['url']);?>" alt="">
        <?php endwhile; endif;?>
    </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tabs-nav a');
    const contents = document.querySelectorAll('.tabs-content .tab');
    const select = document.getElementById('tabs-select');

    // Función para activar un tab por ID
    function activateTab(id) {
        // Desactivar todos
        tabs.forEach(t => t.parentElement.classList.remove('active'));
        contents.forEach(c => c.classList.remove('active'));

        // Activar el correspondiente
        const link = document.querySelector(`.tabs-nav a[href="#${id}"]`);
        if (link) link.parentElement.classList.add('active');

        const content = document.getElementById(id);
        if (content) content.classList.add('active');
    }

    // Modo tabs (desktop)
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('href').substring(1); // quita el #
            activateTab(id);
            // También actualizamos el select si está visible
            if (select) select.value = id;
        });
    });

    // Modo dropdown (mobile)
    if (select) {
        select.addEventListener('change', function() {
            activateTab(this.value);
        });
    }
});




</script>



</main>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiperGaleria = new Swiper('.swiper-galeria', {
        loop: true,
        spaceBetween: 25,
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 1,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('myModal');
    const btn = document.querySelector('.btnModal');
    const closeBtn = modal.querySelector('.close');

    // Abrir modal
    btn.addEventListener('click', () => {
        modal.style.display = 'flex';
    });

    // Cerrar modal con la X
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Cerrar modal haciendo clic afuera del contenido
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

</script>
<?php get_footer(); ?>