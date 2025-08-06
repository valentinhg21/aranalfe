<?php 

function newsletter_menu() {
    add_menu_page(
        'Newsletter', // Título de la página
        'Newsletter', // Texto del menú
        'manage_options', // Capacidad requerida para ver el menú
        'mi-plugin-config', // ID único de la página
        'newsletter_opciones', // Función que renderiza la página
        'dashicons-email',
        60

    );
}
add_action('admin_menu', 'newsletter_menu');

function newsletter_opciones() {
    ?>
    <div class="wrap">
        <h2>Configuración Newsletter</h2>
        <?php
        if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
            ?>
            <div id="message" class="updated notice is-dismissible">
                <p><?php _e( 'Cambios guardados.', 'mi-text-domain' ); ?></p>
            </div>
            <?php
        } elseif ( isset( $_GET['settings-error'] ) && $_GET['settings-error'] ) {
            ?>
            <div id="message" class="error notice is-dismissible">
                <p><?php _e( 'Hubo un error al guardar los cambios. Por favor, inténtalo de nuevo.', 'mi-text-domain' ); ?></p>
            </div>
            <?php
        }
        ?>
 
        <form method="post" action="options.php">
            <?php
            // Agrega los campos de opciones
            settings_fields('newsletter-panel');
            // Renderiza los campos de opciones
            do_settings_sections('newsletter-panel');
            // Botón de guardado
            submit_button('Guardar cambios');
            ?>
        </form>
    </div>
    <?php
}


function newsletter() {
    // Formulario Newsletter
    add_settings_section(
        'newsletter_seccion_general', // ID único de la sección
        'Configuración General', // Título de la sección
        'content_general', // Callback para el contenido de la sección
        'newsletter-panel' // ID de la página donde se mostrará la sección
    );
    // Destinatario
    add_settings_field(
        'newsletter_destinatario', // ID único del campo
        'Destinatario', // Título del campo
        'content_destinatario_callback', // Callback para el contenido del campo
        'newsletter-panel', // ID de la página donde se mostrará el campo
        'newsletter_seccion_general' // ID de la sección donde se mostrará el campo
    );
    // Opcion Email
    add_settings_field(
        'newsletter_email', // ID único del campo
        'Placeholder', // Título del campo
        'content_email_callback', // Callback para el contenido del campo
        'newsletter-panel', // ID de la página donde se mostrará el campo
        'newsletter_seccion_general' // ID de la sección donde se mostrará el campo
    );


    // HTML
    add_settings_section(
        'newsletter_seccion_html', // ID único de la sección
        'HTML ', // Título de la sección
        'content_title', // Callback para el contenido de la sección
        'newsletter-panel' // ID de la página donde se mostrará la sección
    );
    add_settings_field(
        'newsletter_title', // ID único del campo
        'Titulo', // Título del campo
        'content_title_callback', // Callback para el contenido del campo
        'newsletter-panel', // ID de la página donde se mostrará el campo
        'newsletter_seccion_html' // ID de la sección donde se mostrará el campo
    );
    
    
    // Registra las opciones
    register_setting('newsletter-panel', 'newsletter_destinatario');
    register_setting('newsletter-panel', 'newsletter_email');
 
    register_setting('newsletter-panel', 'newsletter_title');
}
add_action('admin_init', 'newsletter');


function content_general() {
    echo '<p>Ingresar correo electrónico del destinatario.</p>';
}
function content_destinatario_callback() {
    $dest = get_option('newsletter_destinatario');
    echo "<input type='text' name='newsletter_destinatario' value='" . esc_attr($dest) . "' />";
}

function content_email_callback() {
    $email = get_option('newsletter_email');
    echo "<input type='text' name='newsletter_email' value='" . esc_attr($email) . "' />";
}



function content_title (){
    echo '<p>Titulo del Newsletter</p>';
}

function content_title_callback(){
    $title = get_option('newsletter_title');
    ?>
     <textarea name="newsletter_title" rows="5" cols="50"><?php echo esc_textarea($title); ?></textarea>
    <?php
}