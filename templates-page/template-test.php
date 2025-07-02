<?php

/**

 * Template Name: TEST

 */

// $development = get_development_by_id(56269)['objects'][0];
// var_dump($development);

// $development = get_development_by_id(56269);
// var_dump($development);
echo "<hr/><p>Llamada básica</p>";
var_dump(get_development_by_id_test(56269));
echo "<hr/><p>Llamada estandar</p>";
var_dump(get_development_by_id(56269)); 
?>
<script>
  const developmentId = 56269;
  const apiKey = 'fad0d191d200804e836be0b26626ac919fa37e8a';
  const url = `https://www.tokkobroker.com/api/v1/development/${developmentId}/?lang=es_ar&format=json&key=${apiKey}`;

  fetch(url)
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      console.log("✅ Resultado de Tokko:", data);
    })
    .catch(error => {
      console.error("❌ Error al obtener datos de Tokko:", error);
    });
</script>
