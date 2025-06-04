<?php 



add_action('init', function () {

    $base = '^propiedades';

    $query_base = 'index.php?pagename=propiedades';



    // Lista de parámetros en orden esperada en la URL

    $params = ['operacion', 'tipo', 'ubicacion', 'localidad', 'antiguedad'];



    // Reglas sin paginación

    for ($i = count($params); $i >= 3; $i--) {

        $pattern = $base;

        $query = $query_base;

        for ($j = 0; $j < $i; $j++) {

            $pattern .= '/([^/]+)';

            $query .= '&' . $params[$j] . '=$matches[' . ($j + 1) . ']';

        }

        $pattern .= '/?$';

        add_rewrite_rule($pattern, $query, 'top');

    }



    // Reglas con paginación

    for ($i = count($params); $i >= 3; $i--) {

        $pattern = $base;

        $query = $query_base;

        for ($j = 0; $j < $i; $j++) {

            $pattern .= '/([^/]+)';

            $query .= '&' . $params[$j] . '=$matches[' . ($j + 1) . ']';

        }

        $pattern .= '/page/([0-9]{1,})/?$';

        $query .= '&paged=$matches[' . ($i + 1) . ']';

        add_rewrite_rule($pattern, $query, 'top');

    }

});



add_filter('query_vars', function ($vars) {

    return array_merge($vars, [

        'operacion',

        'tipo',

        'ubicacion',

        'localidad',

        'antiguedad',

        'paged',

    ]);

});