<?php
function hierarchicTableSubtreeByParent($parent) {
    return array_map(
        function($row) {
            return ['row' => $row, 'children' => hierarchicTableSubtreeByParent($row) ];
        },
        array_filter(
            $_REQUEST['rows'], 
            function($row) use ($parent) { return $_REQUEST['parent']($row, $_REQUEST['rows']) === $parent; }
        )
    );
}
if (!isset($_REQUEST['parent']) && !isset($_REQUEST['children'])) throw new Exception('Either "parent" or "children" callable attribute must be set.');
if (!isset($_REQUEST['parent'])) $_REQUEST['parent'] = function($row, $rows) {
    foreach(array_keys($rows) as $i) if (in_array($row, $_REQUEST['children']($rows[$i], $rows))) return $rows[$i];
    return null;
};
// This is O(n^2). Beware large datasets
$_REQUEST['rows'] = array_map(
    function($row) {
        return [ 'row' => $row, 'children' => hierarchicTableSubtreeByParent($row) ];
    },
    array_filter(
        $_REQUEST['rows'], 
        function($row) {
            return is_null($_REQUEST['parent']($row, $_REQUEST['rows']));
        }
    )
);
$table = $_REQUEST;