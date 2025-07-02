<?php 
// 1. Agregar intervalo personalizado de 30 segundos
// add_filter('cron_schedules', function($schedules) {
//     $schedules['cada_60_segundos'] = [
//         'interval' => 60,
//         'display' => 'Cada 60 segundos'
//     ];
//     return $schedules;
// });

// 2. Activar el cron al iniciar el theme
function activar_cron_locations() {
    if (!wp_next_scheduled('actualizar_locations_event')) {
        wp_schedule_event(time(), 'hourly', 'actualizar_locations_event');
    }
}
add_action('after_setup_theme', 'activar_cron_locations');

// 3. Acción que se ejecuta cada 30 segundos
add_action('actualizar_locations_event', 'save_data_locations');

// 4. Limpiar el cron si se desactiva el theme o plugin
register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook('actualizar_locations_event');
});