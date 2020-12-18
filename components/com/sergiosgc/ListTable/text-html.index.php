<table>
 <thead>
  <tr>
<?php foreach ($listTable['columns'] as $field => $column) { ?>
   <th class="<?= htmlspecialchars(isset($column['class']) ? $column['class'] : $field) ?>">
    <?= htmlspecialchars(isset($column['label']) ? $column['label'] : $field) ?>
   </th>
<?php } ?>
  </tr>
 </thead>
 <tbody>
<?php foreach ($listTable['rows'] as $row) { ?>
  <tr>
<?php foreach ($listTable['columns'] as $field => $column) { ?>
   <td class="<?= htmlspecialchars(isset($column['class']) ? $column['class'] : $field) ?>">
<?php
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
<?php } ?>
<?php if (isset($listTable['emptymessage']) && 0 == count($listTable['rows'])) { ?>
 <tr><td class="emptymessage" colspan="<?= count($listTable['columns']) ?>"><?= $listTable['emptymessage' ] ?></td></tr>
<?php } ?>
 </tbody>
</table> 
