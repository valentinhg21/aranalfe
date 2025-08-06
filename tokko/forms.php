<?php
$jsonResponse = array('status'=>'ERROR','message'=>'No se ha podido enviar el contacto, por favor intente nuevamente');

$no_page = true;
require get_template_directory() . '/tokko/core.php';

$consulta="";
if($_REQUEST['mensaje']){$consulta=$_REQUEST['mensaje'];}
$nombre="";
if($_REQUEST['nombre']){$nombre=$_REQUEST['nombre'];}
$mail="";
if($_REQUEST['email']){$mail=$_REQUEST['email'];}
$phone="";
if($_REQUEST['telefono']){$phone=$_REQUEST['telefono'];}

if (isset($_REQUEST['propiedad_id'])){
     $propiedades = (int)$_REQUEST['propiedad_id'];
     $data = array(
        'text' => $consulta,
        'name' => $nombre,
        'email' => $mail,
        'phone' => $phone,
    );
}
else if(isset($_REQUEST['emprendimiento_id']))
{
    $emprendimiento = (int)($_REQUEST['emprendimiento_id']);
    $data = array(
        'text' => $consulta,
        'name' => $nombre,
        'email' => $mail,
        'phone' => $phone,
        'tags' => $tags,
        'developments' => $emprendimiento
    );
}
else
{
    $data = array(
        'text' => $consulta,
        'name' => $nombre,
        'email' => $mail,
        'phone' => $phone,
        'tags' => $tags
     );
}

//function getRecaptcha($data)
/*{
    try {
        $url = 'https://www.google.com/recaptcha/api/siteverify?secret='.$data['secret'].'&response='.$data['response'];
        $cp = curl_init();
        curl_setopt($cp, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cp, CURLOPT_URL, $url);
        curl_setopt($cp, CURLOPT_TIMEOUT, 60);
        $respuesta = json_decode(curl_exec($cp));
        curl_close($cp);
        return $respuesta;
    } catch (Exception $e) {
        return array('status'=>'false');
    }
}*/

//$recaptcha = getRecaptcha([
//    'secret' => '',
//    'remoteip' => $_SERVER['HTTP_HOST'] ,
//    'response' => $_POST['g-recaptcha-response']
//]);

//if($recaptcha->success == true){
    $webcontact = new TokkoWebContact($auth, $data);
    $response = $webcontact->send();
    $jsonResponse['status'] = 'OK';
    $jsonResponse['message'] = 'Muchas por su contacto, le responderemos a la brevedad';
//} else {$jsonResponse['message'] = 'Código de reCaptcha inválido, por favor tilde nuevamente la casilla de "No soy un robot".';}

echo json_encode($jsonResponse); 
?>
