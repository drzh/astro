<!DOCTYPE html>
<html>
<?php include "../head.php"; ?>
<body>
    <?php
    include '../menu.php';
    require_once __DIR__ . '/../includes/table.php';

    if (isset($_GET['tb'])) {
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
            $tb,
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

    include '../tail.php';
    ?>
</body>

</html>
