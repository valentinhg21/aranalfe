


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