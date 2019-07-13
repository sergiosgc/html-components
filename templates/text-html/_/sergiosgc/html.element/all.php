<?php
if (!isset($tvars['html.element'])) throw new \Exception('Component template requires $tvars[\'html.element\']');
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