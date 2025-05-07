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

