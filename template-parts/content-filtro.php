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

                            <label class="<?= $checked ?>">

                                <?php if (!empty($opcion['url'])): ?>

                                    <button class="button-filter" type="button" data-href="<?= esc_url($opcion['url']) ?>" data-value="<?= esc_attr($opcion['value']) ?>" data-param="<?= esc_attr($opcion['param']) ?>">

                                <?php endif; ?>


                                    <div class="checkmark"></div>

                                    <?= esc_html($opcion['label']) ?>

                                <?php if (!empty($opcion['url'])): ?>

                                    </button>

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



<!-- Precios -->
<div class="filter filter-price">
  <p>Precios</p>
  <div class="input-min-max">
    <div>
      <?php 
        $input_price_min = $_GET['precio_min'] ?? '1';
        $val_price_min_clean = preg_replace('/[^\d]/', '', $input_price_min);
        $val_price_min_clean = $val_price_min_clean !== '' ? (int)$val_price_min_clean : 1;
        $val_price_min_display = number_format($val_price_min_clean, 0, '', '.');
      ?>
      <label for="precio_min_display">Mínimo:</label>
      <input type="text" class="price-input" data-target="precio_min" id="precio_min_display" value="<?= esc_attr($val_price_min_display) ?>">
      <input type="hidden" name="precio_min" id="precio_min" value="<?= esc_attr($val_price_min_clean) ?>">
    </div>
    <div class="separator"><span>-</span></div>
    <div>
      <?php 
        $input_price_max = $_GET['precio_max'] ?? '999999999';
        $val_price_max_clean = preg_replace('/[^\d]/', '', $input_price_max);
        $val_price_max_clean = $val_price_max_clean !== '' ? (int)$val_price_max_clean : 999999999;
        $val_price_max_display = number_format($val_price_max_clean, 0, '', '.');
      ?>
      <label for="precio_max_display">Máximo:</label>
      <input type="text" class="price-input" data-target="precio_max" id="precio_max_display" value="<?= esc_attr($val_price_max_display) ?>">
      <input type="hidden" name="precio_max" id="precio_max" value="<?= esc_attr($val_price_max_clean) ?>">
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
</div>

<!-- Superficie -->
<div class="filter filter-sup">
  <p>Superficie en m<sup>2</sup></p>
  <div class="input-min-max">
    <?php 
      $input_sup_min = $_GET['sup_min'] ?? '1';
      $val_sup_min_clean = preg_replace('/[^\d]/', '', $input_sup_min);
      $val_sup_min_clean = $val_sup_min_clean !== '' ? (int)$val_sup_min_clean : 1;
      $val_sup_min_display = number_format($val_sup_min_clean, 0, '', '.');

      $input_sup_max = $_GET['sup_max'] ?? '999999';
      $val_sup_max_clean = preg_replace('/[^\d]/', '', $input_sup_max);
      $val_sup_max_clean = $val_sup_max_clean !== '' ? (int)$val_sup_max_clean : 999999;
      $val_sup_max_display = number_format($val_sup_max_clean, 0, '', '.');
    ?>
    <div>
      <label for="sup_min_display">Mínimo:</label>
      <input type="text" class="price-input" data-target="sup_min" id="sup_min_display" value="<?= esc_attr($val_sup_min_display) ?>">
      <input type="hidden" name="sup_min" id="sup_min" value="<?= esc_attr($val_sup_min_clean) ?>">
    </div>
    <div class="separator"><span>-</span></div>
    <div>
      <label for="sup_max_display">Máximo:</label>
      <input type="text" class="price-input" data-target="sup_max" id="sup_max_display" value="<?= esc_attr($val_sup_max_display) ?>">
      <input type="hidden" name="sup_max" id="sup_max" value="<?= esc_attr($val_sup_max_clean) ?>">
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.price-input').forEach(input => {
    input.addEventListener('input', function() {
        let clean = this.value.replace(/[^\d]/g, '');
        let targetId = this.dataset.target;
        document.getElementById(targetId).value = clean;
        this.value = clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    });
});
</script>

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

                                    <button class="button-filter" type="button" data-href="<?= esc_url($opcion['url']) ?>">

                                <?php endif; ?>

                                    <input type="checkbox"

                                        name="<?= esc_attr($name) ?>[]"

                                        id="<?= esc_attr($id) ?>"

                                        <?= $checked ?>>

                                    <div class="checkmark"></div>

                                    <?= esc_html($opcion['label']) ?>

                                <?php if (!empty($opcion['url'])): ?>

                                    </button>

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

<div class="clean-filter">
        <button type="button" class="btn btn-red w-100 mb-2 mt-2" id="apply">Aplicar filtros</button>
        <button type="button" class="btn btn-red-outline w-100" id="limpiar-filtros">Limpiar filtros</button>
</div>


