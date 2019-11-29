<?php
$breadcrumbs = $tvars['breadcrumbs'];

printf('<span class="breadcrumbs">%s</span>', 
    implode('<span class="breadcrumb-separator"></span>', array_map(
        function($label, $url) {
            return sprintf('<a href="%s" class="breadcrumb">%s</a>', htmlspecialchars($url), $label);
        },
        array_keys($tvars['breadcrumbs']),
        $tvars['breadcrumbs']
    ))
); 