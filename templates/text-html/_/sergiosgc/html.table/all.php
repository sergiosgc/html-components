<?php
if (!isset($tvars['html.table'])) throw new \Exception('Component template requires $tvars[\'html.table\']');
(function($element) {
    foreach (['content', 'columns'] as $param) if (!isset($element[$param])) throw new \Exception("Missing $param on html.table"); 
    $rows = [ ];
    $elementContent = is_callable($element['content']) ? call_user_func($elementContent, $element) : $element['content'];
    foreach ($elementContent as $index => $cellContent) {
        if ($index % $element['columns'] == 0) $rows[] = [ 'element' => 'tr', '@class' => count($rows) % 2 == 0 ? 'odd' : 'even', 'children' => [] ];
        $yCoord = count($rows);
        $xCoord = $index % $element['columns'];
        $cellContent = is_callable($cellContent) ? call_user_func($cellContent, $element, $xCoord, $yCoord) : $cellContent;
        if (!is_array($cellContent)) $cellContent = [ 'text' => (string) $cellContent ];
        if (isset($cellContent['../@class'])) {
            $rows[$yCoord - 1]['@class'] .= ' ' . $cellContent['../@class'];
            unset($cellContent['../@class']);
        }
        if (count($cellContent) && !isset($cellContent[0])) $cellContent = [ $cellContent ];
        $rows[$yCoord - 1]['children'][] = [ 
            'element' => count($rows) == 1 && $element['header_row']    ?? false || 
                         $xCoord == 1      && $element['header_column'] ?? false ? 
                         'th' : 'td',
            'children' => $cellContent 
        ];
    }
    if ($element['header_row'] ?? false) {
        $table = [ 'element' => 'table', 'children' => [
                    [ 'element' => 'thead', 'children' => [ $rows[0] ]],
                    [ 'element' => 'tbody', 'children' => array_slice($rows, 1) ]
        ]];
    } else {
        $table = [ 'element' => 'table', 'children' => $rows ];
    }
    foreach ($element as $key => $value) if ($key[0] == '@') $table[$key] = $value;
    \sergiosgc\output\Negotiated::$singleton->template('/_/sergiosgc/html.element/', ['html.element' => $table ]);
})($tvars['html.table']);
/*
(function($element) {
    if (is_string($element)) $element = json_decode($element, true, 512, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
    $stack = [ [ $element, false ] ];
    while (count($stack)) {
        list($node, $opened) = array_pop($stack);
        if ($opened) {
            printf('</%s>', $node['element']);
            continue;
        } 
        if (isset($node['element'])) {
            array_push($stack, [ $node, true ]);
            printf('<%s>',
                implode(
                    ' ',
                    array_filter(array_merge(
                        [ $node['element'] ],
                        array_map(
                            function($argName, $argValue) {
                                if ($argName[0] != '@') return '';
                                return sprintf('%s="%s"', htmlspecialchars(substr($argName, 1)), htmlspecialchars($argValue));
                            },
                            array_keys($node),
                            $node
                        )
                    ))
            ));
            if (array_key_exists('children', $node)) for($i=count($node['children'])-1; $i>=0; $i--) array_push($stack, [ $node['children'][$i], false ]);
        } elseif (isset($node['text'])) {
            print(htmlspecialchars($node['text']));
        } elseif (isset($node['raw'])) {
            print($node['raw']);
        }
    }
})($tvars['html.element']);
*/