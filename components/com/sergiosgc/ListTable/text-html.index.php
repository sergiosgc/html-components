<table class="sergiosgc-listtable">
 <thead>
  <tr>
<?php foreach ($listTable['columns'] as $field => $column) {
    $data = [];
    if (array_key_exists('filterable', $column)) {
        $column['class'] = (isset($column['class']) ? $column['class'] : $field) . ' filterable';
        $data['filterable-arg'] = $column['filterable']['argument'] ?? 'filterby';
        $data['filterable-value'] = $column['filterable']['value'] ?? $field;
        if ($data['filterable-filtered'] ?? false) $column['class'] .= ' filtered';
    }
    if (array_key_exists('sortable', $column)) {
        $column['class'] = (isset($column['class']) ? $column['class'] : $field) . ' sortable';
        $data['sortable-arg'] = $column['sortable']['argument'] ?? 'sortby';
        $data['sortable-value'] = $column['sortable']['value'] ?? $field;
        $data['sortable-directions'] = $column['sortable']['directions'] ?? [ 'ASC', 'DESC' ];
        $data['sortable-sorted'] = $column['sortable']['sorted'] ?? false;
        if ($data['sortable-sorted'] ?? false) $column['class'] .= sprintf(' sorted-%s', $data['sortable-sorted']);
    }
?>
   <th class="<?= htmlspecialchars(isset($column['class']) ? $column['class'] : $field) ?>"<?php 
foreach($data as $key => $value) printf(' data-%s="%s"', htmlspecialchars($key), htmlspecialchars(is_string($value) ? $value : json_encode($value,  JSON_THROW_ON_ERROR |  JSON_UNESCAPED_UNICODE )));
?>>
    <?= htmlspecialchars(isset($column['label']) ? $column['label'] : $field) ?><?php if (array_key_exists('filterable', $column)) {
        print('<div class="sergiosgc-filter-button">⋮</div>');
        print('<form class="sergiosgc-filter">');
        foreach($column['filterable']['options'] as $key => $label) printf('<label><input type="checkbox" value="%s"%s><span>%s</span></label>',
            $key,
            in_array($key, $column['filterable']['filter']) ? ' checked' : '',
            $label
        );
        printf('<button class="primary">%s</button>', $column['filterable']['action_label'] ?? __('Filter'));
        print('</form>');
    }?>
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
<script type="text/javascript">
// Sortable column event handling
(function() {
    let table = document.currentScript.previousElementSibling;
    const clickHandlerFunction = function(ev) {
        if (! (ev.target.dataset.sortableArg && ev.target.dataset.sortableValue && ev.target.dataset.sortableDirections && ev.target.dataset.sortableSorted)) return;
        let destination = new URL(window.location);
        destination.searchParams.set(ev.target.dataset.sortableArg, 
            ev.target.dataset.sortableValue
            + ","
            + (function() {
                const [currentValue,currentDirection] = (function() {
                    if (!destination.searchParams.get(ev.target.dataset.sortableArg)) return [null, null];
                    result = destination.searchParams.get(ev.target.dataset.sortableArg).split(",", 2);
                    if (result.length != 2) return [null, null];
                    return result;
                })();
                let sortableDirections = JSON.parse(ev.target.dataset.sortableDirections);
                if (currentValue != ev.target.dataset.sortableValue) return sortableDirections[0];
                return sortableDirections[(sortableDirections.indexOf(currentDirection) + 1) % sortableDirections.length];
            })()
        );
        window.location.href = destination;
    };
    Array.from(document.evaluate('.//th', table, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null)).forEach(element => { element.addEventListener('click', clickHandlerFunction); });
})();
// Filterable column event handling
(function() {
    let table = document.currentScript.previousElementSibling;
    const popupClickHandlerFunction = function(ev) {
        let button = ev.target;
        let dialog = ev.target.nextElementSibling;
        button.classList.toggle('active');
        dialog.style.visibility = button.classList.contains('active') ? 'visible' : 'hidden';
    };
    Array.from(document.evaluate('.//th/div[@class="sergiosgc-filter-button"]', table, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null)).forEach(element => { element.addEventListener('click', popupClickHandlerFunction); });
    const submitClickHandlerFunction = function(ev) {
        const button = ev.target;
        const form = button.form;
        const th = document.evaluate('(.//ancestor::th)[last()]', button, null, XPathResult. FIRST_ORDERED_NODE_TYPE, null).singleNodeValue;
        let destination = new URL(window.location);
        destination.searchParams.set(th.dataset.filterableArg, 
            [ th.dataset.filterableValue ].concat(
                Array.from(document.evaluate('.//input[@type="checkbox"]', form, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null))
                .filter( input => input.checked )
                .filter( input => input.value != '')
                .map( input => input.value )
            ).join(',')
        );
        if (destination.searchParams.get(th.dataset.filterableArg) == th.dataset.filterableValue) destination.searchParams.delete(th.dataset.filterableArg);
        ev.preventDefault();
        ev.stopPropagation();
        window.location.href = destination;
    }
    Array.from(document.evaluate('.//th/form[@class="sergiosgc-filter"]/button', table, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null)).forEach(element => { element.addEventListener('click', submitClickHandlerFunction); });
    const allNoneClickHandlerFunction = function(ev) {
            Array.from(document.evaluate('(./ancestor::form)[last()]//input[@type="checkbox"]', ev.target, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null))
                .forEach( input => input.checked = ev.target.checked );
    }
    Array.from(document.evaluate('(.//th//form//input[@type="checkbox"])[1]', table, null, XPathResult.UNORDERED_NODE_ITERATOR_TYPE, null))
        .filter( input => input.value == '')
        .forEach( element => element.addEventListener('click', allNoneClickHandlerFunction));
})();
</script>