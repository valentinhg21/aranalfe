<?php
if (!wp_next_scheduled('export_properties_to_gsheet_daily')) {
    wp_schedule_event(time(), 'daily', 'export_properties_to_gsheet_daily');
}

add_action('export_properties_to_gsheet_daily', 'export_all_properties_to_google_sheet');