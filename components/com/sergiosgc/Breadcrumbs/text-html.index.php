<?php
if (isset($_REQUEST['breadcrumbs']) && count ($_REQUEST['breadcrumbs'])) printf('<span class="breadcrumbs">%s</span>', 
    implode('<span class="breadcrumb-separator">&#8203;</span>', array_map(
        function($label, $url) {
            return sprintf('<a href="%s" class="breadcrumb">%s</a>', htmlspecialchars($url), $label);
        },
        array_keys($_REQUEST['breadcrumbs']),
        $_REQUEST['breadcrumbs']
    ))
); 