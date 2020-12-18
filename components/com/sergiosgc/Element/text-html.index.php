<?php
print('<');
print(implode(' ', array_filter(array_merge(
    [ $_REQUEST['tagname'] ],
    array_map(
        function ($k, $v) {
            return sprintf('%s="%s"', htmlspecialchars($k), htmlspecialchars($v));
        },
        array_keys($_REQUEST['properties']),
        $_REQUEST['properties']
    )
))));
    if ($_REQUEST['content']) {
        printf('>%s</%s>', $_REQUEST['content'], $_REQUEST['tagname']);
    } else {
        switch (strtolower($_REQUEST['tagname'])) {
            case 'area':
            case 'base':
            case 'br':
            case 'col':
            case 'embed':
            case 'hr':
            case 'img':
            case 'input':
            case 'link':
            case 'meta':
            case 'param':
            case 'source':
            case 'track':
            case 'wbr':
            case 'command':
            case 'keygen':
            case 'menuitem':
                print(' />');
                break;
            default:
                printf('></%s>', $_REQUEST['tagname']);
                break;
        }
    }