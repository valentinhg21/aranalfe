<?php 
define('ROOT', get_template_directory_uri());
define('CSS', ROOT . '/dist/css' );
define('IMAGE', ROOT . '/dist/img' );
define('JS', ROOT . '/dist/js');
define('LIB', ROOT . '/lib');
define('BLOCK', ROOT . '/blocks');
define('IMAGE_DEFAULT', ROOT . '/dist/img/image-default/default.png');
define('SVG', '/dist/img/svg');
define('IMAGE_RELATIVE', '/dist/img/');
// URL PARA LAS REDIRECCIONES DE TOKKO DEPENDIENDO DEL LOCAL
$GLOBALS['tokko_call_count'] = 0;
$GLOBALS['tokko_call_log'] = [];
function contar_llamada_api($endpoint) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'IP desconocida';
    $GLOBALS['tokko_call_count']++;
    $GLOBALS['tokko_call_log'][] = $endpoint;
    error_log("[TOKKO API] Llamada a: {$endpoint} | IP: {$ip}");
}

require_once ('inc/performance/performance.php');
require_once ('inc/login/login.php');
require_once ('class/class-tgm-plugin-activation.php');
require_once ('class/requerid-plugins.php');
require_once ('class/class-wp-walker.php');
require_once ('tokko-api/helpers/helper.php');
require_once ('tokko-api/url/rewrite.php');
require_once ('tokko-api/development.php');
require_once ('tokko-api/locations.php');
require_once ('tokko-api/tag.php');
require_once ('tokko-api/property.php');
require_once ('tokko-api/webContact.php');
require_once ('tokko-api/cache/cache-tokko.php');
require_once ('tokko-api/cron/cron-tokko.php');
require_once ('inc/register-theme.php');
require_once ('inc/register-text-editor.php');
require_once ('inc/register-scripts-style.php');
require_once ('inc/register-functions.php');
require_once ('inc/register-menus.php');
require_once ('inc/register-blocks.php');
require_once ('inc/register-newsletter.php');
require_once ('inc/register-forms.php');
require_once ('inc/register-sync-acf.php');
require_once ('inc/register-theme-options.php');
require_once ('inc/register-cron.php');

