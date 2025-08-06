<?php
require get_template_directory() . '/tokko/api.inc';
$key = 'fad0d191d200804e836be0b26626ac919fa37e8a';

// $key = '90056a0b47eb9bc8b995124778d8f7498ddbe96a';


$auth = new TokkoAuth($key);
// devuelve las propiedades destacadads


function get_destacadas($cant = 12, $operation_type = null)
{
    global $auth;
    $starred_data = '{"current_localization_id":0,"current_localization_type":"country","price_from":1,"price_to":999999999999,"operation_types":[1,2,3], "property_types":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25],"currency":"ANY","filters":[["is_starred_on_web","=",true]]}';

    if ($operation_type !== null) {
        $datajson = json_decode($starred_data);
        unset($datajson->operation_types);
        $datajson->operation_types = array($operation_type);
        $starred_data = json_encode($datajson);
    }

    $tokko_search = new TokkoSearch($auth, $starred_data);
    $tokko_search->do_search($cant, "full_random", "desc");
    $res = $tokko_search->get_properties();

    return $res;
}

function get_emp_desctacados($cant = 12)
{
    global $auth;

    $array_filtro = array();
    array_push($array_filtro, array('key' => 'is_starred_on_web', 'value' => 'true'));

    $emp = new TokkoDevelopmentList($auth, $array_filtro, $cant);
    return $emp->get_developments();
}

function t_limit($string, $length = 50, $dots = "...")
{
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}

function d_ren($dir)
{
    return str_replace(' ', '-', str_replace('/', '-', $dir));
}

function cmpMayorCantidad($a, $b)
{
    if ($a->count == $b->count) {
        return 0;
    }
    return ($a->count < $b->count) ? 1 : -1;
}

function obtenerFiltros()
{
    global $auth;
    $searchData = '{"price_from":1,"price_to":999999999999,"operation_types":[1,2,3],"property_types":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],"currency":"ANY","filters":[]}';
    $tokko_search = new TokkoSearch($auth, $searchData);
    $tokko_search->get_summary();
    usort($tokko_search->summary->objects->property_types, 'cmpMayorCantidad');
    usort($tokko_search->summary->objects->operation_types, 'cmpMayorCantidad');
    usort($tokko_search->summary->objects->locations, 'cmpMayorCantidad');
    return $tokko_search->summary->objects;
}

function obtenerPartidos()
{
    $states = new TokkoStates(1); /* 1 es country argentina */
    return $states->states;
}

function obtenerLocalidadesxPartido($partidoID)
{
    $divisions = new TokkoDivisions($partidoID);
    return $divisions->divisions;
}

function obtenerBarrioxLocalidad($localidadID)
{
    $divisions = new TokkoDivisions(null, $localidadID);
    return $divisions->divisions;
}

function connectApi($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url . "&key=90056a0b47eb9bc8b995124778d8f7498ddbe96a",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache",
            "Postman-Token: 104d6166-4e36-428c-92f2-217dede3b89f"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}


function get_utlimos_ingresos($cant = 12, $operation_type = null, $property_type = null)
{
    global $auth;

    if ($property_type == 14) {
        $starred_data = '{"current_localization_id":0,"current_localization_type":"country","price_from":1,"price_to":999999999999,"operation_types":[1,2,3], "property_types":[14,12,8],"currency":"ANY","filters":[["is_starred_on_web","=",true]]}';
    } else {
        $starred_data = '{"current_localization_id":0,"current_localization_type":"country","price_from":1,"price_to":999999999999,"operation_types":[1,2,3], "property_types":[2,3],"currency":"ANY","filters":[["is_starred_on_web","=",true]]}';
    }

    if ($operation_type !== null) {
        $datajson = json_decode($starred_data);
        unset($datajson->operation_types);
        $datajson->operation_types = array($operation_type);
        $starred_data = json_encode($datajson);
    }


    $tokko_search = new TokkoSearch($auth, $starred_data);
    $tokko_search->do_search($cant, "created_at", "desc");
    $res = $tokko_search->get_properties();

    return $res;
}


function getLocationsAutocomplete()
{
    $merge_location[] = json_decode(connectApi('https://www.tokkobroker.com/api/v1/state/146/?format=json&key=90056a0b47eb9bc8b995124778d8f7498ddbe96a'), true);
    $merge_location[] = json_decode(connectApi('https://www.tokkobroker.com/api/v1/state/147/?format=json&key=90056a0b47eb9bc8b995124778d8f7498ddbe96a'), true);
    $merge_location[] = json_decode(connectApi('https://www.tokkobroker.com/api/v1/state/148/?format=json&key=90056a0b47eb9bc8b995124778d8f7498ddbe96a'), true);
    $merge_location[] = json_decode(connectApi('https://www.tokkobroker.com/api/v1/state/149/?format=json&key=90056a0b47eb9bc8b995124778d8f7498ddbe96a'), true);
    $merge_location[] = json_decode(connectApi('https://www.tokkobroker.com/api/v1/state/150/?format=json&key=90056a0b47eb9bc8b995124778d8f7498ddbe96a'), true);
    $merge_location[] = json_decode(connectApi('https://www.tokkobroker.com/api/v1/state/151/?format=json&key=90056a0b47eb9bc8b995124778d8f7498ddbe96a'), true);

    $todas = [];
    foreach ($merge_location as $location) {
        foreach ($location["divisions"] as $division) {
            $d = [
                "label" => $division["name"] . ", " . $location["name"],
                "id" => $division["id"],
            ];
            $todas[] = $d;
        }
    }
    return $todas;
}
 