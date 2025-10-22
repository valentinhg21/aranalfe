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
define('TOKKO_LOG', false);
// URL PARA LAS REDIRECCIONES DE TOKKO DEPENDIENDO DEL LOCAL


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
require_once ('inc/panel-adm/panel-adm.php');

