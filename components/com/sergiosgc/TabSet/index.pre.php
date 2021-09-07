<?php
global $_com_sergiosgc_htmlComponents_tabsetStack;
if (!isset($_com_sergiosgc_htmlComponents_tabsetStack)) $_com_sergiosgc_htmlComponents_tabsetStack = [];
array_push($_com_sergiosgc_htmlComponents_tabsetStack, [
    'name' => $_REQUEST['name'],
    'active' => $_REQUEST['active'],
    'tabs' => []
]);