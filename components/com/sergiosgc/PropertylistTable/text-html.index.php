<?php
printf(<<<EOS
<table class="%s">
 <tbody>
%s 
 </tbody>
</table>
EOS
    , $_REQUEST['class'] ?? "sergiosgc-propertylisttable", 
        \sergiosgc\ArrayAdapter::from($_REQUEST['properties'] ?? [])->map(function($property, $propertyName) {
            return [ 'class' => sprintf('%s-%s', $_REQUEST['class'], $propertyName), 'label' => $property['label'] ?? $property['title'] ];
        })->zip(
            \sergiosgc\ArrayAdapter::from($_REQUEST['properties'] ?? [])->map(function($property, $propertyName) {
                if (!array_key_exists($propertyName, (array) $_REQUEST['value']) && 
                    !array_key_exists('content', (array) $property) &&
                    !array_key_exists('links', (array) $property)) throw new \Exception('No value set for field ' . $propertyName);
                    
                    $value = $_REQUEST['value'][$propertyName] ?? null;
                    if (is_callable($value)) {
                        return call_user_func($value, $propertyName, $property, $_REQUEST);
                    } elseif (isset($property['content'])) {
                        return is_callable($property['content'])
                                ? call_user_func($property['content'], $value, $propertyName, $property, $_REQUEST['value'], $_REQUEST)
                                : \sergiosgc\printf($property['content'], $value);
                    } elseif (isset($property['links'])) {
                        return sprintf('<ul class="com-sergiosgc-propertylist-table-links">%s</ul>', \sergiosgc\ArrayAdapter::from(
                            is_callable($property['links'])
                                ? call_user_func($property['links'], $value, $propertyName, $property, $_REQUEST['value'], $_REQUEST)
                                : $property['links']
                        )->map(function($link) {
                            return sprintf('<li><a%s href="%s">%s</a></li>', 
                                isset($link['class']) ? sprintf(' class="%s"', $link['class']) : '',
                                \sergiosgc\sprintf($link['href'], $_REQUEST['value']),
                                \sergiosgc\sprintf(strtr($link['label'], [ ' ' => '&nbsp;' ]), $_REQUEST['value'])
                            );
                        })->implode(''));
                    } else {
                        return \sergiosgc\sprintf(
                            $property['format'] ?? false ? $property['format'] : sprintf('%%<%s>', $propertyName), 
                            $_REQUEST['value']
                        );
                    }
            })
        )->map(function($row) { 
            return sprintf('<tr class="%s"><th>%s</th><td>%s</td></tr>',
                $row[0]['class'],
                $row[0]['label'] == '' ? ' ' : $row[0]['label'],
                $row[1] == '' ? ' ' : $row[1]);
        })->implode()
);
