<?php
if (!isset($tvars['list'])) throw new Exception("List component requires \$tvars['list'] to be set");
?>
<?php if (isset($tvars['list']['container'])) ob_start() ?>
<table>
 <thead>
  <tr>
<?php foreach ($tvars['list']['columns'] as $field => $column) { ?>
   <th class="<?= htmlspecialchars(isset($column['class']) ? $column['class'] : $field) ?>">
    <?= htmlspecialchars(isset($column['label']) ? $column['label'] : $field) ?>
   </th>
<?php } ?>
  </tr>
 </thead>
 <tbody>
<?php foreach ($tvars['list']['rows'] as $row) { ?>
  <tr>
<?php foreach ($tvars['list']['columns'] as $field => $column) { ?>
   <td class="<?= htmlspecialchars(isset($column['class']) ? $column['class'] : $field) ?>">
<?php
    if (is_callable($row)) {
        $row($field, $column);
    } elseif (isset($column['content'])) {
     throw new Exception('Unimplemented');
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
                $column['links']
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
 </tbody>
</table> 
<?php 
if (isset($tvars['list']['container'])) {
    $container = $tvars['list']['container'];
    if (!isset($container['children'])) $container['children'] = [];
    $container['children'][] = [ 'raw' => ob_get_clean() ];
    print(\app\FormHelper::html($container));
}

