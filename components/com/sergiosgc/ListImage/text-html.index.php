<?php
$callOrSprintf = function($generator, $row, $listImage) {
    if (is_callable($generator)) return call_user_func($generator, $row, $listImage);
    return \sergiosgc\sprintf($generator, $row);
};
printf('<ul class="%s">', $listImage['class'] ?? 'sergiosgc-listimage');
foreach($listImage['rows'] as $row) {
    printf('<li>');
    if ($listImage['content']['content.top'] ?? false) print(call_user_func($callOrSprintf, $listImage['content']['content.top'], $row, $listImage));
    if (is_callable($listImage['content']['image'])) {
        $image = call_user_func($listImage['image'], $row, $listImage);
    } else {
        $attrs = [];
        foreach($listImage['content']['image'] as $attr => $attrValue) $attrs[] = sprintf('%s="%s"', $attr, htmlspecialchars(call_user_func($callOrSprintf, $attrValue, $row, $listImage)));
        printf('<img %s>', implode(' ', $attrs));
    }
    if ($listImage['content']['content.bottom'] ?? false) print(call_user_func($callOrSprintf, $listImage['content']['content.bottom'], $row, $listImage));
    printf('</li>');
}
print('</ul>');