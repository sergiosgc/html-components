<?php
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
?>
----
<ul class="$class" id="$id">
<![CDATA[<?php foreach ($items as $item) { ?>]]>
 <li class="$item['class']"><a href="$item['href']">$item['label']</a>
<![CDATA[<?php  if ($item['submenu']) { ?>]]>
  <com.sergiosgc.menu-unordered-list items="$item['submenu']" />
<![CDATA[<?php  } ?>]]>
 </li>
<![CDATA[<?php } ?>]]>
</ul>