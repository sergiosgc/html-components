<ul class="<?= isset($tvars['menu']['class']) ? $tvars['menu']['class'] : 'menu' ?>"<?= isset($tvars['menu']['id']) ? ' id="' . $tvars['menu']['id'] . '"' : '' ?>>
<?php 
foreach ($tvars['menu']['items'] as $item) { 
    if (isset($item['active']) && $item['active']) $item['class'] = isset($item['class']) ? $item['class'] . ' active' : 'active';
    if (isset($item['submenu']) && $item['submenu']) $item['class'] = isset($item['class']) ? $item['class'] . ' hassubmenu' : 'hassubmenu';
    printf('<li class="%s"><a href="%s">%s</a>%s</li>',
        array_reduce(
            explode('/', $item['href']),
            function ($acc, $part) { if ($part == "") return $acc; return $acc . ' ' . array_reverse(explode(' ', $acc))[0] . '-' . $part; },
            (isset($item['class']) ? $item['class'] . ' ': '') . 'root'
        ),
        $item['href'], 
        $item['label'],
        isset($item['submenu']) ? \sergiosgc\output\Negotiated::$singleton->stemplate('/_/sergiosgc/menu.ul/', [ 'menu' => [ 'items' => $item['submenu'] ]]) : ''
    );
}
?>
</ul>