<?php $piso = get_field( 'cantidad_de_pisos' ); ?>

<div class="modal">
    <div class="container p-relative">
        <button type="button" class="btn-close">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="row">
            <div class="col-sm-4 col-12">
                <div class="content">
                    <h2>Planos</h2>
                    <div class="select-container select-modal select-simple-list">
                        <div class="field-container-input__icon">
                            <i class="fa-solid fa-chevron-down"></i>
                        </div>
                        <input type="text" readonly value="" id="cantidad-de-pisos" data-type="input-select">
                        <div class="list-select">
                            <ul>
                            <?php if (have_rows('planos')) : ?>
                                <?php while (have_rows('planos')) : the_row(); ?>
                                    <?php if (have_rows('plano')) : ?>
                                        <?php while (have_rows('plano')) : the_row(); ?>
                                            <?php 
                                                $piso = get_sub_field('piso');
                                                if (!$piso) continue;
                                                $slug = sanitize_title(strtolower(trim($piso)));
                                            ?>
                                            <li class="options-list-select select-piso">
                                                <p data-type="piso-<?php echo esc_attr($slug); ?>">Piso <?php echo esc_html($piso); ?></p>
                                            </li>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                <?php endwhile; ?>
                            <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 col-12">
                <div class="planos">
                    <?php if (have_rows('planos')) : ?>
                        <?php while (have_rows('planos')) : the_row(); ?>
                            <?php if (have_rows('plano')) : ?>
                                <?php while (have_rows('plano')) : the_row(); ?>

                                    <?php 
                                    $imagenes = get_sub_field('imagen');
                                    $piso = get_sub_field('piso');
                                    $slug = sanitize_title(strtolower(trim($piso)));
                                    $active = $piso == 1 ? '' : 'd-none';
                                    ?>

                                    <div class="plano <?php echo $active; ?>" id="piso-<?php echo $slug; ?>">
                                        <?php if ($imagenes) : 
                                            $total = count($imagenes);
                                            if ($total > 1) : ?>
                                                <div class="splide splide-planos" aria-label="Galería de planos">
                                                    <div class="splide__track">
                                                        <ul class="splide__list">
                                                            <?php foreach ($imagenes as $image) : ?>
                                                                <li class="splide__slide">
                                                                    <a href="<?php echo esc_url($image['url']); ?>" data-fancybox="piso-galeria">
                                                                        <?php insert_image($image); ?>
                                                                    </a>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php else : 
                                                $image = $imagenes[0]; ?>
                                                <a href="<?php echo esc_url($image['url']); ?>" data-fancybox="piso-galeria">
                                                    <?php insert_image($image); ?>
                                                </a>
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <p class="error-msg d-none">No hay imagen para mostrar</p>
                                        <?php endif; ?>
                                    </div>

                                <?php endwhile; ?>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>


        </div>
    </div>
</div>