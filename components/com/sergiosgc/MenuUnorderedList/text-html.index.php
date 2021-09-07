<?php
(function() {
    // Unpack variables in scope
    foreach (func_get_args()[0] as $veryRandomString137845ToAvoidCollisionsKey => $veryRandomString137845ToAvoidCollisionsValue) $$veryRandomString137845ToAvoidCollisionsKey = $veryRandomString137845ToAvoidCollisionsValue;
    unset($veryRandomVariableNameForThescriptFile);
    unset($veryRandomString137845ToAvoidCollisionsKey);
    unset($veryRandomString137845ToAvoidCollisionsValue);
    // Template PHP code
?><?php
$class = isset($_REQUEST['class']) ? $_REQUEST['class'] : 'menu';
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$items = array_map(
        function($item) { 
            $item['class'] = implode(' ', array_keys(array_flip(array_filter([
                isset($item['class']) ? $item['class'] : '',
                isset($item['active']) && $item['active'] ? 'active' : '',
                isset($item['submenu']) && $item['submenu'] ? 'hassubmenu' : '',
                array_reduce(
                    explode('/', $item['href']),
                    function ($acc, $part) { if ($part == "") return $acc; return $acc . ' ' . array_reverse(explode(' ', $acc))[0] . '-' . $part; },
                    'root'
                ),
            ]))));
            $item['submenu'] = isset($item['submenu']) ? $item['submenu'] : false;
            return $item;
        },
        $_REQUEST['items']);

    // Template components
?><ul class="<?= strtr(@$class, [ '&' => '&amp;', '"' => '&quot;' ]) ?>" id="<?= strtr(@$id, [ '&' => '&amp;', '"' => '&quot;' ]) ?>"><?php ob_start();?><?php foreach ($items as $item) { ?><li class="<?= strtr(@$item['class'], [ '&' => '&amp;', '"' => '&quot;' ]) ?>"><?php ob_start();?><a href="<?= strtr(@$item['href'], [ '&' => '&amp;', '"' => '&quot;' ]) ?>"><?php ob_start();print(@$item['label']);print(ob_get_clean()); print('</a>'); // a
?><?php  if ($item['submenu']) { ?><?php ob_start(); // com/sergiosgc/MenuUnorderedList

\app\Template::componentPre(
    'com/sergiosgc/MenuUnorderedList',
    [
        'items' => @$item['submenu']
    ]
);

\app\Template::component(
    'com/sergiosgc/MenuUnorderedList',
    [
        'content' => ob_get_clean(),
        'items' => @$item['submenu']
    ]
);
?><?php  } ?><?php print(ob_get_clean()); print('</li>'); // li
?><?php } ?><?php print(ob_get_clean()); print('</ul>'); // ul
})(get_defined_vars());