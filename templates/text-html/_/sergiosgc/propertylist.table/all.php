<?php
if (!isset($tvars['property-list'])) throw new Exception("Property list component requires \$tvars['property-list'] to be set");
$propertyList = $tvars['property-list'];
\sergiosgc\output\Negotiated::$singleton->template('/_/sergiosgc/html.element/', [ 'html.element' => [
        'element' => 'table',
        '@class' => $tvars['property-list']['class'],
        'children' => [ [
            'element' => 'tbody',
            'children' => array_map(
                function($column, $columnProperties) use ($propertyList) {
                    if (!array_key_exists($column, $propertyList['value']) && 
                        !array_key_exists('content', $columnProperties) &&
                        !array_key_exists('links', $columnProperties)) throw new \Exception('property-list value has no field ' . $column);

                    $value = @$propertyList['value'][$column];
                    if (is_callable($value)) {
                        $td = [ 'element' => 'td', 'children' => [[ 'raw' => call_user_func($value, $column, $columnProperties, $propertyList) ]] ];
                    } elseif (isset($columnProperties['content'])) {
                        $td = [ 'element' => 'td', 'children' => [[ 'raw' => 
                            is_callable($columnProperties['content']) ?
                                call_user_func($columnProperties['content'], $value, $column, $columnProperties, $propertyList['value'], $propertyList) :
                                \sergiosgc\printf($columnProperties['content'], $value)
                        ]] ];
                    } elseif (isset($columnProperties['links'])) {
                        if (is_callable($columnProperties['links'])) {
                            $links = call_user_func($columnProperties['links'], $value, $column, $columnProperties, $propertyList['value'], $propertyList);
                        } else {
                            $links = $columnProperties['links'];
                        }
                        $td = [ 'element' => 'td', 'children' => [[ 'raw' => implode('&nbsp;|&nbsp;',
                            array_map(
                                function($link) use ($propertyList) {
                                    return sprintf('<a%s href="%s">%s</a>', 
                                        isset($link['class']) ? sprintf(' class="%s"', $link['class']) : '',
                                        \sergiosgc\sprintf($link['href'], $propertyList['value']),
                                        \sergiosgc\sprintf(strtr($link['label'], [ ' ' => '&nbsp;' ]), $propertyList['value'])
                                    );
                                },
                                $links
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
]]);
