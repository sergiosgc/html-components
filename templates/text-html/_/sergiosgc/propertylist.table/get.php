<?php
if (!isset($tvars['property-list'])) throw new Exception("Property list component requires \$tvars['property-list'] to be set");
(function($propertyList) {
    $GLOBALS['tvars']['html.element'] = [
        'element' => 'table',
        '@class' => $tvars['property-list']['class'],
        'children' => [ [
            'element' => 'tbody',
            'children' => array_map(
                function($column, $columnProperties) use ($propertyList) {
                    if (!isset($propertyList['value'][$column]) && !isset($columnProperties['links'])) throw new \Exception('property-list value has no field ' . $column);
                    $value = $propertyList['value'][$column];
                    if (is_callable($value)) {
                        $td = [ 'element' => 'td', 'children' => [[ 'raw' => call_user_func($value, $column, $columnProperties, $propertyList) ]] ];
                    } elseif (isset($columnProperties['links'])) {
                        $td = [ 'element' => 'td', 'children' => [[ 'raw' => implode('&nbsp;|&nbsp;',
                            array_map(
                                function($link) use ($propertyList) {
                                    return sprintf('<a%s href="%s">%s</a>', 
                                        isset($link['class']) ? sprintf(' class="%s"', $link['class']) : '',
                                        \sergiosgc\sprintf($link['href'], $propertyList['value']),
                                        \sergiosgc\sprintf(strtr($link['label'], [ ' ' => '&nbsp;' ]), $propertyList['value'])
                                    );
                                },
                                $columnProperties['links']
                            )
                        ) ]] ];
                    } else {
                        $format = isset($columnProperties['format']) ? $columnProperties['format'] : sprintf('%%<%s>', $column);
                        $td = [ 'element' => 'td', 'children' => [[ 'raw' => \sergiosgc\sprintf($format, $propertyList['value']) ]] ];
                    }
                    return [
                        'element' => 'tr',
                        '@class' => sprintf('%s-%s', $propertyList['class'], $column),
                        'children' => [
                            [
                                'element' => 'th',
                                'children' => [[ 'text' => $columnProperties['label'] ]]
                            ],
                            $td
                        ]
                    ];
                },
                array_keys($propertyList['properties']),
                $propertyList['properties'])
        ] ]
];
\sergiosgc\output\Negotiated::$singleton->template('/_/sergiosgc/html.element/');
})($tvars['property-list']);
