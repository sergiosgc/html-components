<?php 
function hierarchicDescriptionListPrintRow($list, $row, $depth) {
    printf('<dl class="%s" data-depth="%d">',
        implode(" ", array_filter( 
            [
                'sergiosgc-hierachic-description-list',
                'sergiosgc-hierachic-description-list-depth-' . $depth,
                isset($_REQUEST['row-class']) ? ( is_callable( $_REQUEST['row-class']) ? $_REQUEST['row-class']($row, $list, $depth) : $_REQUEST['row-class'] ) : false
            ]
        )), 
        $depth
    );
    foreach($row['children'] as $child) {
        printf('<dt class="%s">%s</dt><dd>', 
            implode(" ", array_filter([ isset($_REQUEST['row-class']) ? ( is_callable( $_REQUEST['row-class']) ? $_REQUEST['row-class']($child, $list, $depth) : $_REQUEST['row-class'] ) : false ])),
            $_REQUEST['description']($child, $list, $depth));
        hierarchicDescriptionListPrintRow($list, $child, $depth+1);
        print('</dd>');
    }
    printf('</dl>');
}

printf('<dl class="%s" data-depth="0">',
    implode(" ", array_filter( 
        [
            'sergiosgc-hierachic-description-list',
            'sergiosgc-hierachic-description-list-depth-0',
            isset($_REQUEST['class']) ? ( is_callable( $_REQUEST['class']) ? $_REQUEST['class']($list) : $_REQUEST['class'] ) : false
        ]
    )));
array_map( function($child) use ($list) {
    printf('<dt>%s</dt><dd>', $_REQUEST['description']($child, $list, 0));
    hierarchicDescriptionListPrintRow($list, $child, 1);
    print('</dd>');
}, $list['rows']);
printf('</dl>');
/*
function hierarchicTablePrintRow($table, $row, $depth) { 
    $children = $row['children'];
    $row = $row['row'];
    ?>
  <tr class="sergiosgc-hierachictable-indent-<?php echo $depth ?> sergiosgc-hierarchictable-<?php echo !isset($table['subtree-callback']) && 0 == count($children) ? "leaf" : "collapsed" ?>" data-sergiosgc-hierachictable-tree-depth="<?php echo $depth ?>">
   <td class="sergiosgc-hierarchictable-tree"> </td>
<?php $indentClass="sergiosgc-hierarchictable-indent"; ?>
<?php foreach ($table['columns'] as $field => $column) { ?>
   <td class="<?= htmlspecialchars(isset($column['class']) ? $column['class'] : $field) ?> <?php echo $indentClass ?>">
<?php
    $indentClass = "";
    if (is_callable($row)) {
        $row($field, $column);
    } elseif (isset($column['content'])) {
        if (is_callable($column['content'])) print(call_user_func($column['content'], $row, $field)); else \sergiosgc\printf($column['content'], $row[$field]);
    } elseif (isset($column['links'])) {
        print(implode('&nbsp;|&nbsp;',
            array_map(
                function($link) use ($row) {
                    return sprintf('<a%s href="%s">%s</a>', 
                        isset($link['class']) ? sprintf(' class="%s"', $link['class']) : '',
                        \sergiosgc\sprintf($link['href'], $row),
                        \sergiosgc\sprintf(strtr($link['label'], [ ' ' => '&nbsp;' ]), $row)
                    );
                },
                is_callable($column['links']) ? call_user_func($column['links'], $row, $field) : $column['links']
            )
        ));
    } else {
        $format = isset($column['format']) ? $column['format'] : sprintf('%%<%s>', $field);
        \sergiosgc\printf($format, $row);
    }
?>
   </td>
<?php } ?>
  </tr>
<?php 
foreach ($children as $child) hierarchicTablePrintRow($table, $child, $depth + 1); 
} 
?>
<table class="<?php echo $_REQUEST['class'] ?? "sergiosgc-hierarchictable" ?>">
 <thead>
  <tr>
   <th class="sergiosgc-hierarchictable-tree"> </th>
<?php foreach ($table['columns'] as $field => $column) { ?>
   <th class="<?= htmlspecialchars(isset($column['class']) ? $column['class'] : $field) ?>">
    <?= htmlspecialchars(isset($column['label']) ? $column['label'] : $field) ?>
   </th>
<?php } ?>
  </tr>
 </thead>
 <tbody>
<?php 
foreach ($_REQUEST['rows'] as $row) hierarchicTablePrintRow($table, $row, 1); 
if (isset($table['emptymessage']) && 0 == count($table['rows'])) { 
?>
 <tr><td class="emptymessage" colspan="<?= count($table['columns']) ?>"><?= $table['emptymessage' ] ?></td></tr>
<?php } ?>
 </tbody>
</table> 
<script type="text/javascript">
window.addEventListener('DOMContentLoaded', (function(table) {
    var setRowDisplay = function(tr) {
        var newDisplay = "";
        if (tr.dataset.sergiosgcHierachictableTreeDepth == "1") {
            newDisplay = "table-row";
        } else {
            var parents = Array
                .from(document.evaluate("preceding-sibling::tr", tr, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null))
                .reduceRight(
                    function(acc, tr) {
                        if (Number(acc[0].dataset.sergiosgcHierachictableTreeDepth) -1 == Number(tr.dataset.sergiosgcHierachictableTreeDepth)) acc.unshift(tr);
                        return acc;
                    },
                    [ tr ]
                );
            parents.pop();
            newDisplay = parents.reduceRight(
                (acc, tr) => tr.classList.contains("sergiosgc-hierarchictable-expanded") ? acc : "none", 
                "table-row"
            );
        }
        tr.style.display = newDisplay;
    }
    Array.from(document.evaluate("./tbody/tr", table, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null)).filter( tr => tr.dataset.sergiosgcHierachictableTreeDepth != "1").forEach( setRowDisplay );
    table.addEventListener('click', function(ev) {
        if (ev.target.tagName != "TD" || !ev.target.classList.contains("sergiosgc-hierarchictable-tree")) return;
        var tr = ev.target.parentNode;
        var toAdd = tr.classList.contains("sergiosgc-hierarchictable-collapsed") ? "sergiosgc-hierarchictable-expanded" : "sergiosgc-hierarchictable-collapsed";
        var toRemove = !tr.classList.contains("sergiosgc-hierarchictable-collapsed") ? "sergiosgc-hierarchictable-expanded" : "sergiosgc-hierarchictable-collapsed";
        tr.classList.remove(toRemove);
        tr.classList.add(toAdd);
        var children = Array.from(document.evaluate("following-sibling::tr", tr, null, XPathResult.ORDERED_NODE_SNAPSHOT_TYPE, null));
        for (node in children) {
            if (Number(children[node].sergiosgcHierachictableTreeDepth) <= Number(tr.dataset.sergiosgcHierachictableTreeDepth)  ) break;
            setRowDisplay(children[node]);
        }
    });
}).bind(this, document.evaluate("(preceding::table)[last()]", document.currentScript, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null).singleNodeValue));
</script>
*/