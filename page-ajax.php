<?php 
    /*
        Template Name: Ajax

    */
    

    $config = require get_template_directory() . '/tokko-api/config.php';
    

    $fullname    = sanitize_text_field($_POST['fullname'] ?? '');
    $telephone   = sanitize_text_field($_POST['telephone'] ?? '');
    $email       = sanitize_email($_POST['email'] ?? '');
    $message     = sanitize_textarea_field($_POST['message'] ?? '');
    $tags        = sanitize_text_field($_POST['tags'] ?? '');
    $property_id = sanitize_text_field($_POST['property_id'] ?? '');
    $development_id = sanitize_text_field($_POST['development_id'] ?? '');
    // Xtras
    $origen = sanitize_text_field($_POST['origen'] ?? '');
    $fuente = sanitize_text_field($_POST['fuente'] ?? '');
    $propertyName = sanitize_text_field($_POST['property_name'] ?? '');
    $propertyType = sanitize_text_field($_POST['property_type'] ?? '');
    $propertyOperation = sanitize_text_field($_POST['property_operation'] ?? '');
    $developmentName = sanitize_text_field($_POST['development_name'] ?? '');
    $contactData = [
        "name"      => $fullname,
        "phone"     => $telephone,
        "email"     => $email,
        "text"      => $message,
        "tags"      => $tags
    ];

    if (!empty($property_id)) {
        $contactData['properties'] = [(int) $property_id];
    }
    if (!empty($development_id)) {
        $contactData['developments'] = [(int) $development_id];
    }
    $drive = "https://script.google.com/macros/s/AKfycbyl6C7DTtqAh1eNJ4ckJvHxzHzdNebhUEzLmzi8hSE7cXeNr36qWqnpaaSNG8QUUew/exec";
    $data_drive = [
        'origen'         => $origen,
        'nombre'         => $fullname,
        'email'          => $email,
        'telefono'       => $telephone,
        'mensaje'        => $message,
        'tokko_id'       => $property_id ?? $development_id,
        'propiedad'      => $propertyName, //
        'tipo_propiedad' => $propertyType, //
        'operacion'      => $propertyOperation, //
        'emprendimiento' => $developmentName, //
        'fuente'         => $fuente,
    ];


    $apiKey = $config['api_token'];
    $response = sendTokkoWebContact($contactData, $apiKey);
 
    if (isset($response['error']) && $response['error'] === true) {
        // Hubo un error
        $jsonResponse = [
            'status' => 'ERROR',
            'message' => $response['message'] ?? 'Error desconocido',
            'details' => $response['response'] ?? null
        ];
    } elseif (isset($response['success']) && $response['success'] === true) {
        // Éxito sin datos extra

        $responseDrive = saveDataGoogleSheet($data_drive, $drive);
        $jsonResponse = [
            'status'  => ($responseDrive && trim($responseDrive) === 'OK') ? 'ERROR' : 'OK',
            'message' => ($responseDrive && trim($responseDrive) === 'OK') ? 'Mensaje no enviado con éxito.' : 'Mensaje enviado con éxito.',
            'response' => $data_drive
        ];
    } else {
        // Éxito con datos devueltos

        $responseDrive = saveDataGoogleSheet($data_drive, $drive);
        $jsonResponse = [
            'status'  => ($responseDrive && trim($responseDrive) === 'OK') ? 'ERROR' : 'OK',
            'message' => ($responseDrive && trim($responseDrive) === 'OK') ? 'Mensaje no enviado con éxito.' : 'Mensaje enviado con éxito.',
            'response' => $data_drive
        ];
    }

    echo json_encode($jsonResponse);
   

    
   

    

 

 