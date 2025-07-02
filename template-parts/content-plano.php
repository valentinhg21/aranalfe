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
                                <?php for ($i = 1; $i < $piso + 1; $i++):?>
                                    <li class="options-list-select select-piso">
                                        <p data-type="piso-<?php echo $i?>">Piso <?php echo $i?> al <?php echo $piso?></p>
                                    </li>
                                <?php endfor; ?>

    
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8 col-12">
                <div class="planos">
                    <?php if ( have_rows( 'planos' ) ) : ?>
                        <?php while ( have_rows( 'planos' ) ) :
                        the_row(); ?>
                            <?php if ( have_rows( 'plano' ) ) : ?>
                                
                                <?php while ( have_rows( 'plano' ) ) :
                                the_row(); ?>
                                    <?php $imagen = get_sub_field( 'imagen' ); 
                                          $piso = get_sub_field( 'piso' );
                                          $active = $piso == 1 ? '' : 'd-none'
                                    ?>
                                    <div class="plano <?php echo $active;?>"   id="piso-<?php echo $piso;?>">
                                        <a href="<?php echo esc_url($imagen['url']);?>" data-fancybox="piso-galeria">
                                            <?php insert_image($imagen); ?>
                                        </a>
                                 
                                    </div>
                                <?php endwhile; ?>
                             
                                    <p class="error-msg d-none">No hay imagén para mostrar</p>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>