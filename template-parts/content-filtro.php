<?php 
$filtros = get_query_var('filtros');
$filtros_tag = get_query_var('filtros_tag');
?> 

<?php if (!empty($filtros) && is_array($filtros)): ?>

    <?php foreach ($filtros as $titulo => $opciones): ?>

        <?php if (!empty($opciones) && is_array($opciones)): ?>
            <?php if($titulo !== 'Ubicación'): ?>
            <div class="filter">

                <p><?= esc_html($titulo) ?></p>

                <ul class="<?= strtolower(esc_attr($titulo)) ?>">

                    <?php foreach ($opciones as $i => $opcion): 

                        $name = 'filter-' . sanitize_title($titulo);

                        $id = $name . '-' . $i;

                        $checked = !empty($opcion['checked']) ? 'checked' : '';

                    ?>

                        <li class="checkbox">

                            <label for="<?= esc_attr($id) ?>" class="<?= $checked ?>">

                                <?php if (!empty($opcion['url'])): ?>

                                    <a href="<?= esc_url($opcion['url']) ?>">

                                <?php endif; ?>

                                    <input type="checkbox"

                                        name="<?= esc_attr($name) ?>[]"

                                        id="<?= esc_attr($id) ?>"

                                        <?= $checked ?>>

                                    <div class="checkmark"></div>

                                    <?= esc_html($opcion['label']) ?>

                                <?php if (!empty($opcion['url'])): ?>

                                    </a>

                                <?php endif; ?>

                            </label>

                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>
            <?php endif; ?>

        <?php endif; ?>

    <?php endforeach; ?>

<?php endif; ?>



<div class="filter filter-price">

     <p>Precios</p>

    <form method="get" action="">

        <?php

        // Mantener otros filtros como hidden inputs

        foreach ($_GET as $key => $value) {

            if (in_array($key, ['moneda', 'precio_min', 'precio_max'])) continue;



            if (is_array($value)) {

                foreach ($value as $v) {

                    echo '<input type="hidden" name="'.esc_attr($key).'[]" value="'.esc_attr($v).'">';

                }

            } else {

                echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';

            }

        }

        ?>

        <div class="input-min-max">

            <div>

                <label for="precio_min">Mínimo:</label>

                <input

                    type="number"

                    name="precio_min"

                    id="precio_min"

                    min="1"

                    value="<?= esc_attr(($val = preg_replace('/[^\d]/', '', $_GET['precio_min'] ?? '')) !== '' ? $val : '1') ?>"/>

            </div>

            <div class="separator"><span>-</span></div>

            <div>

                <label for="precio_max">Máximo:</label>

                <input

                    type="number"

                    name="precio_max"

                    id="precio_max"

                    min="0"

                    value="<?= esc_attr(($val = preg_replace('/[^\d]/', '', $_GET['precio_max'] ?? '')) !== '' ? $val : '999999999') ?>"/>

            </div>

        </div>

        <div class="currency-price">

            <label class="<?= ($_GET['moneda'] ?? '') === 'USD' ? 'checked' : '' ?>">

                <input class="btn-currency-price" type="radio" name="moneda" value="USD"

                    <?= ($_GET['moneda'] ?? '') === 'USD' ? 'checked' : '' ?>> USD

            </label>

            <label class="<?= ($_GET['moneda'] ?? '') === 'ARS' ? 'checked' : '' ?>">

                <input class="btn-currency-price" type="radio" name="moneda" value="ARS"

                    <?= ($_GET['moneda'] ?? '') === 'ARS' ? 'checked' : '' ?>> ARS

            </label>

        </div>

        <button type="submit">Aplicar</button>

    </form>

</div>



<div class="filter filter-sup">
     <p>Superficie en m<sup>2</sup></p>
    <form method="get" action="">
        <?php
        // Mantener otros filtros como hidden inputs
        foreach ($_GET as $key => $value) {
            if (in_array($key, ['sup_min', 'sup_max'])) continue;



            if (is_array($value)) {

                foreach ($value as $v) {

                    echo '<input type="hidden" name="'.esc_attr($key).'[]" value="'.esc_attr($v).'">';

                }

            } else {

                echo '<input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'">';

            }

        }

        ?>

        <div class="input-min-max">

            <div>

                <label for="sup_min">Mínimo:</label>

                <input

                    type="number"

                    name="sup_min"

                    id="sup_min"

                    min="0"

                    value="<?= esc_attr(($val = preg_replace('/[^\d]/', '', $_GET['sup_min'] ?? '')) !== '' ? $val : '1') ?>"/>

            </div>

            <div class="separator"><span>-</span></div>

            <div>

                <label for="sup_max">Máximo:</label>

                <input

                    type="number"

                    name="sup_max"

                    id="sup_max"

                    min="0"

                    value="<?= esc_attr(($val = preg_replace('/[^\d]/', '', $_GET['sup_max'] ?? '')) !== '' ? $val : '999999') ?>"/>

            </div>

        </div>



        <button type="submit">Aplicar</button>

    </form>

</div>

<?php if (!empty($filtros_tag) && is_array($filtros_tag)): ?>

    <?php foreach ($filtros_tag as $titulo => $opciones): ?>

        <?php if (!empty($opciones) && is_array($opciones)): ?>
            <?php if($titulo !== 'Servicios' && $titulo !== 'Otros'): ?>
            <div class="filter">
                
                <p><?= esc_html($titulo) ?></p>

                <ul>

                    <?php foreach ($opciones as $i => $opcion): 

                        $name = 'filter-' . sanitize_title($titulo);

                        $id = $name . '-' . $i;

                        $checked = !empty($opcion['checked']) ? 'checked' : '';

                    ?>

                        <li class="checkbox">

                            <label for="<?= esc_attr($id) ?>">

                                <?php if (!empty($opcion['url'])): ?>

                                    <a href="<?= esc_url($opcion['url']) ?>">

                                <?php endif; ?>

                                    <input type="checkbox"

                                        name="<?= esc_attr($name) ?>[]"

                                        id="<?= esc_attr($id) ?>"

                                        <?= $checked ?>>

                                    <div class="checkmark"></div>

                                    <?= esc_html($opcion['label']) ?>

                                <?php if (!empty($opcion['url'])): ?>

                                    </a>

                                <?php endif; ?>

                            </label>

                        </li>

                    <?php endforeach; ?>

                </ul>
                
            </div>
            <?php endif; ?>
        <?php endif; ?>

    <?php endforeach; ?>

<?php endif; ?>

<div clas="clean-filter">

    <form method="get" action="/aranalfe/propiedades/">

        <button type="submit" class="btn btn-red-outline w-100">

               Limpiar Filtros                 

        </button>

    </form>

</div>