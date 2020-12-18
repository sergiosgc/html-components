<?php
// Validate inputs
if (!isset($paginator['page'])) throw new Exception('Paginator "page" must be set');
if (!isset($paginator['pagecount'])) throw new Exception('paginator "pagecount" must be set');
foreach([
    'showonsinglepage' => true,
    'visible' => 3, 
    'linkhref' => '%<page>', 
    'liklabel' => '%<page>', 
    'startlinklabel' => '|&lt;',
    'endlinklabel' => '&gt;|',
    'skipuplinklabel' => '&gt;&gt;',
    'skipdownlinklabel' => '&lt;&lt;',
    'class' => 'paginator',
    'preservequeryarguments' => false,
    'queryargumentswhitelist' => [],
    'queryargumentsblacklist' => []
    ] as $setting => $default) if (!isset($paginator[$setting])) $paginator[$setting] = $default;
foreach(['page', 'pagecount', 'visible'] as $setting) $paginator[$setting] = (int) $paginator[$setting];
if (0 == $paginator['pagecount']) return;
$paginator['showonsinglepage'] = is_string($paginator['showonsinglepage']) ? ($paginator['showonsinglepage'] == 't' || $paginator['showonsinglepage'] == 'true' || $paginator['showonsinglepage'] == '1') : (bool) $paginator['showonsinglepage'];
if (!$paginator['showonsinglepage'] && 1 == $paginator['pagecount']) return;
if ($paginator['visible'] % 2 == 0) throw new Exception('$tvars[\'paginator\'][\'visible\'] must be an odd number');
$paginator['queryargumentswhitelist'] = is_array($paginator['queryargumentswhitelist']) ? $paginator['queryargumentswhitelist'] : explode(',', (string) $paginator['queryargumentswhitelist']);
$paginator['queryargumentsblacklist'] = is_array($paginator['queryargumentsblacklist']) ? $paginator['queryargumentsblacklist'] : explode(',', (string) $paginator['queryargumentsblacklist']);
$queryArgs = [];
foreach ($_GET as $key => $val) {
    if ($paginator['preservequeryarguments'] && !in_array($key, $paginator['queryargumentsblacklist']) ||
        !$paginator['preservequeryarguments'] && in_array($key, $paginator['queryargumentswhitelist'])) {

        $queryArgs[$key] = $val;
    }
}
if (strpos($paginator['linkhref'], '#')) {
    list($paginator['linkhref'], $paginator['linkHash']) = explode('#', $paginator['linkhref'], 2);
    $paginator['linkHash'] = '#' . $paginator['linkHash'];
} else {
    $paginator['linkHash'] = '';
}
if (count($queryArgs)) {
    $paginator['linkhref'] .= 
        (strpos($paginator['linkhref'], '?') === FALSE ? '?' : '&') . 
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
printf('<span class="%s">', $paginator['class']);
$startPage = $paginator['page'] - ($paginator['visible'] - 1) / 2;
$endPage = $startPage + $paginator['visible'] - 1;
if ($startPage < 1) {
    $endPage += 1 - $startPage;
    $startPage += 1 - $startPage;
}
if ($endPage > $paginator['pagecount']) $endPage = $paginator['pagecount'];
if ($endPage < $startPage) $endPage = $startPage;
if ($startPage == 1) {
    \sergiosgc\printf('<span class="%<class>-start %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($paginator['startlinklabel'], $paginator),
        $paginator);
} else {
    \sergiosgc\printf('<span class="%<class>-start"><a href="%s%s">%s</a></span>', 
        \sergiosgc\sprintf($paginator['linkhref'], array_merge($paginator, [ 'page' => 1 ])),
        $paginator['linkHash'],
        \sergiosgc\sprintf($paginator['startlinklabel'], array_merge($paginator, [ 'page' => 1 ])),
        $paginator);
}
if ($startPage - ($paginator['visible'] - 1) / 2 <= 1) {
    \sergiosgc\printf('<span class="%<class>-skipDown %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($paginator['skipdownlinklabel'], $paginator),
        $paginator);
} else {
    \sergiosgc\printf('<span class="%<class>-skipDown"><a href="%s%s">%s</a></span>', 
        \sergiosgc\sprintf($paginator['linkhref'], array_merge( $paginator, ['page' => max(1, $paginator['page'] - $paginator['visible']) ])),
        $paginator['linkHash'],
        \sergiosgc\sprintf($paginator['skipdownlinklabel'], array_merge( $paginator, ['page' => max(1, $paginator['page'] - $paginator['visible']) ])),
        $paginator);
}
for ($page = $startPage; $page <= $endPage; $page++) \sergiosgc\printf('<span class="%<class>-page%s"><a href="%s%s">%s</a></span>', 
        $page == $paginator['page'] ? ' current' : '',
        \sergiosgc\sprintf($paginator['linkhref'], array_merge($paginator, [ 'page' => $page ])),
        $paginator['linkHash'],
        \sergiosgc\sprintf($paginator['liklabel'], array_merge($paginator, [ 'page' => $page ])),
        $paginator);
if ($endPage + ($paginator['visible'] - 1) / 2 >= $paginator['pagecount']) {
    \sergiosgc\printf('<span class="%<class>-skipUp %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($paginator['skipuplinklabel'], $paginator),
        $paginator);
} else {
    \sergiosgc\printf('<span class="%<class>-skipUp"><a href="%s%s">%s</a></span>', 
        \sergiosgc\sprintf($paginator['linkhref'], array_merge( $paginator, ['page' => min($paginator['pagecount'], $paginator['page'] + $paginator['visible']) ])),
        $paginator['linkHash'],
        \sergiosgc\sprintf($paginator['skipuplinklabel'], array_merge( $paginator, ['page' => min($paginator['pagecount'], $paginator['page'] + $paginator['visible']) ])),
        $paginator);
}
if ($endPage == $paginator['pagecount']) {
    \sergiosgc\printf('<span class="%<class>-end %<class>-nolink">%s</span>', 
        \sergiosgc\sprintf($paginator['endlinklabel'], $paginator),
        $paginator);
} else {
    \sergiosgc\printf('<span class="%<class>-end"><a href="%s%s">%s</a></span>', 
        \sergiosgc\sprintf($paginator['linkhref'], array_merge($paginator, [ 'page' => $paginator['pagecount'] ])),
        $paginator['linkHash'],
        \sergiosgc\sprintf($paginator['endlinklabel'], array_merge($paginator, [ 'page' => $paginator['pagecount'] ])),
        $paginator);
}
printf('</span>');
