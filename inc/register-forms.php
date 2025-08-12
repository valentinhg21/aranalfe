<?php  
add_action('wp_ajax_send_form_contact', 'send_form_contact');
add_action('wp_ajax_nopriv_send_form_contact', 'send_form_contact');
function send_form_contact() {

    $empresa = 'Empresa';

    // Incluir PHPMailer si no está cargado

    if (!class_exists('PHPMailer')) {

        require_once ABSPATH . WPINC . '/class-phpmailer.php';

        require_once ABSPATH . WPINC . '/class-smtp.php';

    }



    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Inicializar PHPMailer

        $mail = new PHPMailer();

        $mail->isMail();

        $mail->setFrom('no-reply@empresa.com', $empresa);

        

        // Configurar opciones de mensaje

        $dataObject = $_POST;

        $dataObjectFiles = $_FILES;

        $messageHTML = "<html><head><title>" . $empresa . "</title></head><body>";

        $subject = isset($dataObject['subject']) ? htmlspecialchars($dataObject['subject']) : 'Sin Asunto';



        // Procesar destinatarios

        $destinatarios = isset($dataObject['destinatario']) ? explode(',', $dataObject['destinatario']) : [];

        $counter = 0;

        

        foreach ($destinatarios as $destinatario) {

            $destinatario = trim($destinatario);

            if (filter_var($destinatario, FILTER_VALIDATE_EMAIL)) {

                if ($counter === 0) {

                    $mail->addAddress($destinatario); // Primer destinatario

                } else {

                    $mail->addBCC($destinatario); // Los demás en copia oculta

                }

                $counter++;

            } else {

                echo "Dirección de correo inválida: $destinatario";

                return;

            }

        }



        // Procesar cuerpo del mensaje

        foreach ($dataObject as $key => $value) {

            if ($key !== "subject" && $key !== "action" && $key !== "destinatario") {

                $formattedValue = htmlspecialchars(str_replace(',', ', ', $value));

                $messageHTML .= "<p><strong>" . ucfirst(strtolower(str_replace('-', ' ', $key))) . ":</strong> $formattedValue</p>";

            }

        }

        

        $messageHTML .= "</body></html>";

        

        // Configuración de correo

        $mail->isHTML(true);

        $mail->CharSet = "UTF-8";

        $mail->Subject = $subject;

        $mail->Body = $messageHTML;



        // Procesar archivos adjuntos

        $uploadPath = get_template_directory() . '/archivo/';

        // Asegurarse de que la carpeta "archivo" exista

        if (!file_exists($uploadPath)) {

            mkdir($uploadPath, 0755, true); // Crear la carpeta con permisos de escritura si no existe

        }



        foreach ($dataObjectFiles as $file) {

            $file_tmp = $file['tmp_name'];

            $file_name = sanitize_file_name($file['name']);

            $file_dest = $uploadPath . $file_name; // Ruta final del archivo



            if (move_uploaded_file($file_tmp, $file_dest)) {

                $mail->addAttachment($file_dest); // Agregar el archivo como adjunto

            } else {

                error_log("Error al mover el archivo: $file_name a $file_dest");

            }

        }



        // Enviar el correo

        if (!$mail->send()) {

            error_log("Mailer Error: " . $mail->ErrorInfo);

            echo "Mailer Error: " . $mail->ErrorInfo;

        } else {

            echo 'ok';

        }

    } else {

        echo 'Método no permitido';

    }

}



add_action('wp_ajax_send_form_newsletter', 'send_form_newsletter');
add_action('wp_ajax_nopriv_send_newsletter', 'send_form_newsletter');
function send_form_newsletter(){

    $empresa = 'Empresa';

    global $mail; // define the global variable

    if (!is_object($mail) || !is_a($mail, 'PHPMailer')) { // check if $phpmailer object of class PHPMailer exists

        // if not - include the necessary files

        require_once ABSPATH . WPINC . '/class-phpmailer.php';

        require_once ABSPATH . WPINC . '/class-smtp.php';

        $mail = new PHPMailer(true);

    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $messageHTML = "<html><head><title>". $empresa  ."</title></head><body>";

        $destinatario = $_POST['destinatario'];

        $email = $_POST['email'];

        $messageHTML .= "<h2><strong>". $empresa . "</strong></h2>";

        $subject = 'Empresa - Newsletter';

        $mail = new PHPMailer();

        $mail->isMail();

        $mail->SetFrom('no-reply@empresa.com', $empresa); // Remitente, cambiar según sea necesario

        $mail->AddAddress($destinatario, $subject);

        $mail->isHTML(true);

        $mail->CharSet = "UTF-8";

        $mail->Subject = $subject;

        $mail->Body= $messageHTML;

        if (!$mail->Send()) {

            echo "Mailer Error: " . $mail->ErrorInfo;

        } else {

            echo 'ok';

        }

    }

}





add_action('wp_ajax_send_form_tokko', 'send_form_tokko');
add_action('wp_ajax_nopriv_send_form_tokko', 'send_form_tokko');
function send_form_tokko() {

        header('Content-Type: application/json');

    require get_template_directory() . '/tokko/core.php';



    $config = require get_template_directory() . '/tokko-api/config.php';

    $jsonResponse = array('status'=>'ERROR','message'=>'No se ha podido enviar el contacto, por favor intente nuevamente');

    $fullname    = sanitize_text_field($_POST['fullname'] ?? '');

    $telephone   = sanitize_text_field($_POST['telephone'] ?? '');

    $email       = sanitize_email($_POST['email'] ?? '');

    $message     = sanitize_textarea_field($_POST['message'] ?? '');

    $tags        = sanitize_text_field($_POST['tags'] ?? '');

    $property_id = sanitize_text_field($_POST['property_id'] ?? '');



    $contactData = [

        "name"      => $fullname,

        "cellphone" => $telephone,

        "phone"     => $telephone,

        "email"     => $email,

        "text"      => $message,

        "tags"      => $tags

    ];



    if (!empty($property_id)) {

        $contactData['properties'] = [(int) $property_id];

    }

    // $apiKey = $config['api_token'];

    $webcontact = new TokkoWebContact($apiKey, $contactData);



    $response = $webcontact->send();

    $jsonResponse['data'] = $contactData;

    $jsonResponse['status'] = 'OK';

    echo json_encode($jsonResponse);

    

   



    // $response = sendTokkoWebContact($contactData, $apiKey);

    // var_dump($response);

    // if ($response === null) {

    //     wp_send_json_error([

    //         'message' => 'Error interno del servidor al enviar el contacto.',

    //         'details' => 'Revisa los logs del servidor para más información.'

    //     ]);

    // } elseif (!isset($response['id'])) {

    //     wp_send_json_error([

    //         'message' => 'Error inesperado de la API de Tokko.',

    //         'response' => $response

    //     ]);

    // } else {

    //     wp_send_json_success([

    //         'message' => '¡Mensaje enviado con éxito!',

    //         'response_data' => $response

    //     ]);

    // }



    wp_die();

}





add_action('wp_ajax_enviar_consulta_tokko', 'enviar_consulta_tokko');
add_action('wp_ajax_nopriv_enviar_consulta_tokko', 'enviar_consulta_tokko');
function enviar_consulta_tokko() {
    $config = require get_template_directory() . '/tokko-api/config.php';
    $fullname    = sanitize_text_field($_POST['fullname'] ?? '');
    $telephone   = sanitize_text_field($_POST['telephone'] ?? '');
    $email       = sanitize_email($_POST['email'] ?? '');
    $message     = sanitize_textarea_field($_POST['message'] ?? '');
    $tags        = sanitize_text_field($_POST['tags'] ?? '');
    $property_id = sanitize_text_field($_POST['property_id'] ?? '');
    $development_id = sanitize_text_field($_POST['development_id'] ?? '');
    $origen = sanitize_text_field($_POST['origen'] ?? '');
    $fuente = sanitize_text_field($_POST['fuente'] ?? '');
    $propertyName = sanitize_text_field($_POST['property_name'] ?? '');
    $propertyType = sanitize_text_field($_POST['property_type'] ?? '');
    $propertyOperation = sanitize_text_field($_POST['property_operation'] ?? '');
    $developmentName = sanitize_text_field($_POST['development_name'] ?? '');
    $propertyAddress = sanitize_text_field($_POST['property_address'] ?? '');
    $propertyBarrio = sanitize_text_field($_POST['property_barrio'] ?? '');
    $propertyPrice = sanitize_text_field($_POST['property_price'] ?? '');
    $propertyPriceAlquiler = sanitize_text_field($_POST['property_price_alquiler'] ?? '');
    $propertyMetros = sanitize_text_field($_POST['property_metros'] ?? '');
    $contactData = [
        "name"  => $fullname,
        "phone" => $telephone,
        "email" => $email,
        "text"  => $message,
        "tags"  => $tags
    ];
    if (!empty($property_id)) {
        $contactData['properties'] = [(int)$property_id];
    }

    if (!empty($development_id)) {
        $contactData['developments'] = [(int)$development_id];
    }
    $data_drive = [
        'origen'         => $origen,
        'nombre'         => $fullname,
        'email'          => $email,
        'telefono'       => $telephone,
        'mensaje'        => $message,
        'tokko_id'       => $property_id ?? $development_id,
        'propiedad'      => $propertyName,
        'tipo_propiedad' => $propertyType,
        'operacion'      => $propertyOperation,
        'emprendimiento' => $developmentName,
        'fuente'         => $fuente,
        'direccion' => $propertyAddress,
        'barrio' => $propertyBarrio,
        'precio_venta' => $propertyPrice,
        'precio_alquiler' => $propertyPriceAlquiler,
        'metros_totales' => $propertyMetros
    ];



    $apiKey = $config['api_token'];
    $response = sendTokkoWebContact($contactData, $apiKey);
    $driveUrl = "https://script.google.com/macros/s/AKfycbyl6C7DTtqAh1eNJ4ckJvHxzHzdNebhUEzLmzi8hSE7cXeNr36qWqnpaaSNG8QUUew/exec";
    $responseDrive = saveDataGoogleSheet($data_drive, $driveUrl);
    // Envío del correo
    $to = 'info@aranalfe.com ';
    $subject = $data_drive['origen'] . ' de ' . $data_drive['nombre']. ' desde la web aranalfe.com';
    $body = "
        Nombre: {$fullname}<br>
        Email: {$email}<br>
        Teléfono: {$telephone}<br>
        Mensaje: {$message}<br><br>
        Propiedad: {$propertyName}<br>
        Tipo: {$propertyType}<br>
        Operación: {$propertyOperation}<br>
        Emprendimiento: {$developmentName}<br>
        Dirrecion: {$propertyAddress}<br>
        Barrio: {$propertyBarrio}<br>
        Precio: {$propertyPrice}<br>
        Precio Alquiler: {$propertyPriceAlquiler}<br>
        Metros Totales: {$propertyMetros}<br>
        Fuente: {$fuente}
    ";

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'Cc: valentin@zetenta.com',
        'Cc: federico@zetenta.com',
    ];



    // Setea no-reply como remitente
    add_filter('wp_mail_from', function () {
        return 'no-responder@aranalfe.com';
    });

    add_filter('wp_mail_from_name', function () {
        return 'Aranalfe Propiedades';
    });

    wp_mail($to, $subject, $body, $headers);



    // Respuesta AJAX
    $jsonResponse = [
        'status'  => ($responseDrive && trim($responseDrive) === 'OK') ? 'ERROR' : 'OK',
        'message' => ($responseDrive && trim($responseDrive) === 'OK') ? 'Mensaje no enviado con éxito.' : 'Mensaje enviado con éxito.',
        'response' => $data_drive
    ];
    wp_send_json($jsonResponse);
    
}


add_action('wp_ajax_get_search', 'get_search');
add_action('wp_ajax_nopriv_get_search', 'get_search');

function get_search() {
    // Leer datos desde $_POST porque viene form-data (no JSON)
    $operation_types = isset($_POST['operation_types']) ? array_map('intval', (array) $_POST['operation_types']) : [1];
    $property_types = isset($_POST['property_types']) ? array_map('intval', (array) $_POST['property_types']) : range(1, 28);

    $params_2 = [
        'data' => [
            'current_localization_type' => 'country',
            'current_localization_id' => 1,
            'price_from' => 0,
            'price_to' => 9999999999999,
            'operation_types' => $operation_types,
            'property_types' => $property_types,
            'currency' => 'USD',
            'filters' => [],
            'with_tags' => []
        ]
    ];

    $summary_data = get_search_summary($params_2) ?? [];

    wp_send_json_success($summary_data);
}