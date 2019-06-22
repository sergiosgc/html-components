<?php
// Validate inputs
if (!isset($tvars['paginator'])) throw new Exception('$tvars[\'paginator\'] must be set');
if (!isset($tvars['paginator']['page'])) throw new Exception('$tvars[\'paginator\'][\'page\'] must be set');
if (!isset($tvars['paginator']['pageCount'])) throw new Exception('$tvars[\'paginator\'][\'pageCount\'] must be set');
foreach([
    'visible' => 3, 
    'linkHref' => '%<page>', 
    'linkLabel' => '%<page>', 
    'startLinkLabel' => '|&lt;',
    'endLinkLabel' => '&gt;|',
    'skipUpLinkLabel' => '&gt;&gt;',
    'skipDownLinkLabel' => '&lt;&lt;',
    'class' => 'paginator',
    'preserveQueryArguments' => false,
    'queryArgumentsWhitelist' => [],
    'queryArgumentsBlacklist' => []
    ] as $setting => $default) if (!isset($tvars['paginator'][$setting])) $tvars['paginator'][$setting] = $default;
foreach(['page', 'pageCount', 'visible'] as $setting) $tvars['paginator'][$setting] = (int) $tvars['paginator'][$setting];
if ($tvars['paginator']['visible'] % 2 == 0) throw new Exception('$tvars[\'paginator\'][\'visible\'] must be an odd number');
$queryArgs = [];
foreach ($_GET as $key => $val) {
    if ($tvars['paginator']['preserveQueryArguments'] && !in_array($key, $tvars['paginator']['queryArgumentsBlacklist']) ||
        !$tvars['paginator']['preserveQueryArguments'] && in_array($key, $tvars['paginator']['queryArgumentsWhitelist'])) {

        $queryArgs[$key] = $val;
    }
}
if (count($queryArgs)) {
    $tvars['paginator']['linkHref'] .= 
        (strpos($tvars['paginator']['linkHref'], '?') === FALSE ? '?' : '&') . 
        strtr(
            implode('&', array_map(
                function($key, $val) {
                    return sprintf('%s=%s', urlencode($key), urlencode($val));
                },
                array_keys($queryArgs),
                $queryArgs)),
                [ '%' => '%%' ]);
}
// Output
printf('<span class="%s">', $tvars['paginator']['class']);
$startPage = $tvars['paginator']['page'] - ($tvars['paginator']['visible'] - 1) / 2;
$endPage = $startPage + $tvars['paginator']['visible'] - 1;
if ($startPage < 1) {
    $endPage += 1 - $startPage;
    $startPage += 1 - $startPage;
}
if ($endPage > $tvars['paginator']['pageCount']) $endPage = $tvars['paginator']['pageCount'];
if ($endPage < $startPage) $endPage = $startPage;
if ($startPage == 1) {
    \sergiosgc\printf('<span class="%<class>-start %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($tvars['paginator']['startLinkLabel'], $tvars['paginator']),
        $tvars['paginator']);
} else {
    \sergiosgc\printf('<span class="%<class>-start"><a href="%s">%s</a></span>', 
        \sergiosgc\sprintf($tvars['paginator']['linkHref'], array_merge($tvars['paginator'], [ 'page' => 1 ])),
        \sergiosgc\sprintf($tvars['paginator']['startLinkLabel'], array_merge($tvars['paginator'], [ 'page' => 1 ])),
        $tvars['paginator']);
}
if ($startPage - ($tvars['paginator']['visible'] - 1) / 2 <= 1) {
    \sergiosgc\printf('<span class="%<class>-skipDown %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($tvars['paginator']['skipDownLinkLabel'], $tvars['paginator']),
        $tvars['paginator']);
} else {
    \sergiosgc\printf('<span class="%<class>-skipDown"><a href="%s">%s</a></span>', 
        \sergiosgc\sprintf($tvars['paginator']['linkHref'], array_merge( $tvars['paginator'], ['page' => max(1, $tvars['paginator']['page'] - $tvars['paginator']['visible']) ])),
        \sergiosgc\sprintf($tvars['paginator']['skipDownLinkLabel'], array_merge( $tvars['paginator'], ['page' => max(1, $tvars['paginator']['page'] - $tvars['paginator']['visible']) ])),
        $tvars['paginator']);
}
for ($page = $startPage; $page <= $endPage; $page++) \sergiosgc\printf('<span class="%<class>-page%s"><a href="%s">%s</a></span>', 
        $page == $tvars['paginator']['page'] ? ' current' : '',
        \sergiosgc\sprintf($tvars['paginator']['linkHref'], array_merge($tvars['paginator'], [ 'page' => $page ])),
        \sergiosgc\sprintf($tvars['paginator']['linkLabel'], array_merge($tvars['paginator'], [ 'page' => $page ])),
        $tvars['paginator']);
if ($endPage + ($tvars['paginator']['visible'] - 1) / 2 >= $tvars['paginator']['pageCount']) {
    \sergiosgc\printf('<span class="%<class>-skipUp %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($tvars['paginator']['skipUpLinkLabel'], $tvars['paginator']),
        $tvars['paginator']);
} else {
    \sergiosgc\printf('<span class="%<class>-skipUp"><a href="%s">%s</a></span>', 
        \sergiosgc\sprintf($tvars['paginator']['linkHref'], array_merge( $tvars['paginator'], ['page' => min($tvars['paginator']['pageCount'], $tvars['paginator']['page'] + $tvars['paginator']['visible']) ])),
        \sergiosgc\sprintf($tvars['paginator']['skipUpLinkLabel'], array_merge( $tvars['paginator'], ['page' => min($tvars['paginator']['pageCount'], $tvars['paginator']['page'] + $tvars['paginator']['visible']) ])),
        $tvars['paginator']);
}
if ($endPage == $tvars['paginator']['pageCount']) {
    \sergiosgc\printf('<span class="%<class>-end %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($tvars['paginator']['endLinkLabel'], $tvars['paginator']),
        $tvars['paginator']);
} else {
    \sergiosgc\printf('<span class="%<class>-end"><a href="%s">%s</a></span>', 
        \sergiosgc\sprintf($tvars['paginator']['linkHref'], array_merge($tvars['paginator'], [ 'page' => $tvars['paginator']['pageCount'] ])),
        \sergiosgc\sprintf($tvars['paginator']['endLinkLabel'], array_merge($tvars['paginator'], [ 'page' => $tvars['paginator']['pageCount'] ])),
        $tvars['paginator']);
}
printf('</span>');