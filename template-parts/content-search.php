<?php 
 $locations = get_only_locations();
 $property_types = get_only_property_types();
 $parent_locations = get_only_parent_locations();

?>

<form class="search">
    <div class="search-container">
        <div class="select-operation-mobile d-none-md">
            <ul>
                <li><button type="button" data-type="1" class="btn-select-operation active"><span>COMPRAR</span></button></li>
                <li><button type="button" data-type="2" class="btn-select-operation"><span>ALQUILER</span></button></li>
            </ul>
        </div>
        <div class="select-operation d-flex-md d-none">
            <div class="select-container select-simple-list">
                <div class="field-container-input__icon">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <input type="text" readonly value="Operación" id="input-type-operation" data-type="input-select">
                <div class="list-select">
                    <ul>
                        <li class="options-list-select">
                            <p data-type="1">Ventas</p>
                        </li>
                        <li class="options-list-select">
                            <p data-type="2">Alquiler</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="select-type">
            <div class="select-container select-simple-list">
                <div class="field-container-input__icon">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <input type="text" readonly placeholder="Tipo de propiedad" data-ids="" value="Tipo de propiedad" id="input-type-property" data-text="Tipo de propiedad">
                <div class="list-select">
                    <ul class="list-type-property">

                        <?php if (!empty($property_types)): ?>
                            <?php 
                                $all_type_ids = [];
                                foreach ($property_types as $types) {
                                    $all_type_ids[] = $types['id'];
                                }
                            ?>


                            <?php foreach ($property_types as $types): ?>
                                <?php
                                    $type_name = $types['type'];
                                    $type_id = $types['id'];
                                ?>
                                <li class="options-list-select">
                                    <p id="<?php echo $type_id; ?>"><?php echo $type_name; ?></p>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="select-location">
            <div class="select-container select-simple-list">
                <div class="field-container-input__icon">
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <input type="text" readonly value="" placeholder="Ubicación" data-ids="" id="input-type-parentlocation" data-text="Ubicación">
                <div class="list-select">
                    <ul class="list-type-parentlocation">
                        <?php if (!empty($parent_locations)): ?>
                            <?php 
                                $all_parent_ids = [];
                                foreach ($parent_locations as $parent) {
                                    $all_parent_ids[] = $parent['parent_id'];
                                }
                            ?>


                            <?php foreach ($parent_locations as $parent): ?>
                                <?php 
                                    $parent_name = $parent['parent_name']; 
                                    $parent_id = $parent['parent_id']; 
                                ?>
                                <li class="options-list-select">
                                    <p id="<?php echo $parent_id; ?>"><?php echo $parent_name ?></p>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="input-search">
            <div class="autocomplete-custom">
                <input type="text" id="input-location" data-ids="" readonly autocomplete="off" placeholder="Ingresá barrio o localidad...">
                <div class="results">
                    <input type="text" name="location"  id="search-location" placeholder="Buscar">
                    <ul class="list-type-location">
                        <?php if (!empty($locations)): ?>
                            <?php 
                                $all_ids = [];
                                $all_parents = [];
                                foreach($locations as $location) {
                                    $all_ids[] = $location['location_id'];
                                    if (!in_array($location['parent_name'], $all_parents)) {
                                        $all_parents[] = $location['parent_name'];
                                    }
                                }
                            ?>

                            <?php foreach($locations as $location): ?>
                                <?php 
                                    $location_name = $location['location_name']; 
                                    $parent_name = $location['parent_name']; 
                                    $location_id = $location['location_id']; 
                                ?>
                                <li class="options-list-select" data-parent="<?php echo $parent_name ?>">
                                    <p id="<?php echo $location_id; ?>"><?php echo $location_name ?></p>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>

        </div>
        <div class="submit">
            <button type="submit" class="btn btn-search" id="search-button">
                <span class="d-none-md">Buscar</span>
                <svg width="33" height="32" viewBox="0 0 33 32" fill="none" xmlns="http://www.w3.org/2000/svg"
                    class="d-flex-md d-none">
                    <ellipse cx="15.125" cy="14.6667" rx="8.25" ry="8" stroke="currentColor" stroke-width="1.5" />
                    <path
                        d="M15.125 10.6667C14.5833 10.6667 14.0469 10.7701 13.5464 10.9711C13.046 11.1722 12.5912 11.4668 12.2082 11.8382C11.8251 12.2097 11.5213 12.6506 11.314 13.1359C11.1067 13.6212 11 14.1414 11 14.6667"
                        stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                    <path d="M27.5 26.6667L23.375 22.6667" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" />
                </svg>
            </button>
        </div>
    </div>
</form>