<?php 

$filtros = get_query_var('filtros');


foreach ($filtros as $titulo => $opciones): ?>
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
<?php endforeach; ?>