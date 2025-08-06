<?php
/**

 * Template Name: TEST TOKKO

 */


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://tokkobroker.com/api/v1/property/get_search_summary/?format=json&lang=es_ar&data=%257B%2522current_localization_id%2522%253A0%252C%2522current_localization_type%2522%253A%2522country%2522%252C%2522price_from%2522%253A1%252C%2522price_to%2522%253A999999999%252C%2522operation_types%2522%253A%255B1%252C2%255D%252C%2522property_types%2522%253A%255B1%252C2%252C3%252C4%252C5%252C6%252C7%252C8%252C9%252C10%252C11%252C12%252C13%252C14%252C15%252C16%252C17%252C18%252C19%252C20%252C21%252C22%252C23%252C24%252C25%255D%252C%2522currency%2522%253A%2522ANY%2522%252C%2522filters%2522%253A%255B%255D%257D&key=fad0d191d200804e836be0b26626ac919fa37e8a',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Cookie: stickounet=1751293715.792.30501.180592'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

print_r($response);

  ?>
