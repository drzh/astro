<?php
require_once __DIR__ . '/includes/table.php';
require_once __DIR__ . '/includes/table_modules.php';

$tbm = $_GET['tbm'] ?? '';
$table_module = null;
$table_module_error = '';
$page_title = null;
if ($tbm !== '') {
    try {
        $table_module = astro_load_table_module($tbm, $_GET);
        $page_title = $table_module['title'] ?? null;
    } catch (Throwable $exc) {
        $table_module_error = $exc->getMessage();
        $page_title = 'Table';
    }
}
?>
<!DOCTYPE html>
<html>
<?php include __DIR__ . '/head.php'; ?>
<body>
    <?php
    include __DIR__ . '/menu.php';

    if ($table_module_error !== '') {
        echo '<section class="panel">';
        echo '<p class="page-note">', htmlspecialchars($table_module_error, ENT_QUOTES, 'UTF-8'), '</p>';
        echo '</section>';
    } elseif ($table_module !== null) {
        astro_render_table_module($table_module);
    } elseif (isset($_GET['tb'])) {
        $tb = $_GET['tb'];
        # Sanitize the input to avoid directory traversal attacks
        $tb = basename($tb);
        $sort_column = $_GET['sort'] ?? $_GET['sort_column'] ?? '';
        $sort_order = astro_normalize_table_sort_order($_GET['order'] ?? $_GET['sort_order'] ?? 'asc');
        $per_page = $_GET['per_page'] ?? $_GET['records_per_page'] ?? 50;
        $pagination_params = array('tb' => $tb);
        if ($sort_column !== '') {
            $pagination_params['sort'] = $sort_column;
            $pagination_params['order'] = $sort_order;
        }

        display_table_from_tsv(
            astro_path('table/data/' . $tb),
            1,
            array(
                'sort_column' => $sort_column,
                'sort_order' => $sort_order,
                'paginate' => true,
                'page' => $_GET['page'] ?? 1,
                'per_page' => $per_page,
                'pagination_params' => $pagination_params,
            )
        );
    }

    include __DIR__ . '/tail.php';
    ?>
</body>

</html>
