<?php 

 $locations = get_only_locations();

 $property_types = get_only_property_types();





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

                <input type="text" readonly value="Operación" data-ids="" id="input-type-operation" data-type="input-select">

                <div class="list-select">

                    <ul>

                        <li class="options-list-select option-type-operation option-comprar">

                            <p data-type="1">Comprar</p>

                        </li>

                        <li class="options-list-select option-type-operation">

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
                <input type="text" readonly placeholder="Tipo de propiedad" data-ids="7" value="Tipo de propiedad" id="input-type-property" data-text="Tipo de propiedad">
                <div class="list-select">
                    <ul class="list-type-property">
                        <li class="options-list-select option-property-types">
                            <p id="7" class="option-property select">Locales</p>
                        </li>
                        <li class="options-list-select option-property-types">
                            <p id="2" class="option-property">Departamento</p>
                        </li>
                        <li class="options-list-select option-property-types">
                            <p id="8" class="option-property">Edificio Comercial</p>
                        </li>
                        <li class="options-list-select option-property-types">
                            <p id="10" class="option-property">Cochera</p>
                        </li>
                        <li class="options-list-select option-property-types">
                            <p id="1" class="option-property">Terreno</p>
                        </li>
                        <li class="options-list-select option-property-types">
                            <p id="5" class="option-property">Oficina</p>
                        </li>
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

                        <?php if ( have_rows( 'locacion_padre', 'options' ) ) : ?>

                            <?php while ( have_rows( 'locacion_padre', 'options' ) ) :

                            the_row(); ?>

                                <?php $id = get_sub_field( 'id', 'options' ); ?>

                                <?php $name = get_sub_field( 'name', 'options' ); ?>

                                <li class="options-list-select option-parent">

                                    <p id="<?php echo $id; ?>" class="option-click-parent"><?php echo $name ?></p>

                                </li>

                            <?php endwhile; ?>

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



                        <?php if ( have_rows( 'barrios', 'options' ) ) : ?>

                            <?php while ( have_rows( 'barrios', 'options' ) ) :

                            the_row(); ?>

                                <?php $id = get_sub_field( 'id', 'options' ); ?>

                                <?php $name = get_sub_field( 'name', 'options' ); ?>

                                <?php $parentName = get_sub_field( 'parent_name', 'options' ); ?>

                                <li class="options-list-select option-location" data-parent="<?php echo $parentName ?>">

                                    <p id="<?php echo $id; ?>"><?php echo $name ?></p>

                                </li>

                            <?php endwhile; ?>

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
        <div class="loader-container">
            <span class="loader"></span>
        </div>
    </div>

</form>