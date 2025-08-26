<?php 

/**
 * Incluye la clase WP_List_Table.
 * Necesaria para crear tablas de administración con estilo de WordPress.
 */
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

/**
 * Clase personalizada para mostrar las locaciones en una tabla.
 * Hereda de WP_List_Table para usar sus funcionalidades.
 */
class Locaciones_List_Table extends WP_List_Table {

    private $data = [];

    public function __construct() {
        parent::__construct([
            'singular' => 'locacion',
            'plural'   => 'locaciones',
            'ajax'     => false,
        ]);
        $this->load_data();
    }

    /**
     * Carga los datos desde el archivo JSON del tema.
     */
    private function load_data() {
        $json_file = get_stylesheet_directory() . '/tokko-api/data/locations.json';
        if (file_exists($json_file)) {
            $json_data = file_get_contents($json_file);
            // Decodifica el JSON y utiliza los nombres de las claves de tu archivo
            $this->data = json_decode($json_data, true);
        }
    }

    /**
     * Define las columnas de la tabla.
     *
     * @return array
     */
    public function get_columns() {
        return [
            'count'         => 'Count',
            'location_id'   => 'ID',
            'location_name' => 'Nombre',
            'parent_id'     => 'ID Padre',
            'parent_name'   => 'Nombre Padre',
        ];
    }

    /**
     * Define las columnas que se pueden ordenar.
     *
     * @return array
     */
    public function get_sortable_columns() {
        return [
            'count'         => ['count', false],
            'location_id'   => ['location_id', false],
            'location_name' => ['location_name', false],
        ];
    }

    /**
     * Define cómo se muestra el contenido de cada celda.
     *
     * @param array  $item
     * @param string $column_name
     * @return mixed
     */
    public function column_default($item, $column_name) {
        return $item[$column_name] ?? '';
    }

    /**
     * Prepara los elementos para la visualización.
     * Incluye la lógica de búsqueda, ordenamiento y paginación.
     */
    public function prepare_items() {
        $this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns()];

        $search_term = $_REQUEST['s'] ?? '';
        $data_filtrada = $this->data;

        // Lógica de búsqueda
        if ($search_term) {
            $data_filtrada = array_filter($this->data, function ($item) use ($search_term) {
                return (
                    stripos($item['location_name'], $search_term) !== false ||
                    stripos($item['parent_name'], $search_term) !== false
                );
            });
        }

        // Lógica de ordenamiento
        usort($data_filtrada, function ($a, $b) {
            $orderby = $_REQUEST['orderby'] ?? 'location_id';
            $order = $_REQUEST['order'] ?? 'asc';
            $result = strnatcasecmp($a[$orderby], $b[$orderby]);
            return ($order === 'desc') ? -$result : $result;
        });

        // Lógica de paginación
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = count($data_filtrada);
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ]);

        $this->items = array_slice($data_filtrada, ($current_page - 1) * $per_page, $per_page);
    }
}

/**
 * Agrega el menú "Locaciones" al panel de administración.
 */
add_action('admin_menu', 'locaciones_menu_page');

function locaciones_menu_page() {
    add_menu_page(
        'Locaciones',             // Título de la página
        'Locaciones',             // Título del menú
        'manage_options',         // Capability para ver el menú
        'locaciones-panel',       // Slug del menú
        'render_locaciones_panel', // Función que genera el contenido de la página
        'dashicons-location-alt', // Ícono del menú
        6                         // Posición en el menú
    );
}

/**
 * Función que renderiza el contenido del panel.
 */
function render_locaciones_panel() {
    $locaciones_table = new Locaciones_List_Table();
    $locaciones_table->prepare_items();
    ?>
    <div class="wrap">
        <h2>Locaciones</h2>
        <form method="get">
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php
            // Muestra el buscador
            $locaciones_table->search_box('Buscar Locación', 'locacion_search_id');
            // Muestra la tabla
            $locaciones_table->display();
            ?>
        </form>
    </div>
    <?php
}