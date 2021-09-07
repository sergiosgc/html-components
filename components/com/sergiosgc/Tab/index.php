<?php
global $_com_sergiosgc_htmlComponents_tabsetStack;
array_push($_com_sergiosgc_htmlComponents_tabsetStack[ count($_com_sergiosgc_htmlComponents_tabsetStack) - 1]['tabs'], [
    'name' => $_REQUEST['name'],
    'label' => $_REQUEST['label']
]);
$_REQUEST['active'] = ($_com_sergiosgc_htmlComponents_tabsetStack[ count($_com_sergiosgc_htmlComponents_tabsetStack) - 1]['active'] ?? "") == $_REQUEST['name'];