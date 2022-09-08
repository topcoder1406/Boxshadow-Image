<?php

const CONFIG = [
    'blur-radius' => 0,
    'spread-radius' => 1,
    'minify-css' => false // Set true to minify css box-shadow code
];

if (!isset($argv[1])) {
    exit('To run script type "php main.php <filename>" and press enter.');
}

if (!file_exists($argv[1])) {
    exit("Can't find file \"$argv[1]\"");
}

if (!file_exists('template.html')) {
    exit("Can't find file \"template.html\". Try reinstall the application.");
}

$inputFile = $argv[1];
if (!@is_array(getimagesize($inputFile))) {
    exit('Bad image format.');
}

$image = imagecreatefromstring(file_get_contents($inputFile));
$width = imagesx($image);
$height = imagesy($image);
$result = '';

function toHexColor(int $color)
{
    $r = ($color >> 16) & 0xFF;
    $g = ($color >> 8) & 0xFF;
    $b = $color & 0xFF;
    return sprintf("#%02x%02x%02x", $r, $g, $b);
}

for ($y = 0; $y < $height; ++$y) {
    for ($x = 0; $x < $width; ++$x) {
        $color = toHexColor(imagecolorat($image, $x, $y));
        $result .= sprintf("%s%dpx %dpx %dpx %dpx %s",
            (($x == 0 && $y == 0) || CONFIG['minify-css']) ? '' : str_repeat("\t", 5),
            $x * CONFIG['spread-radius'],
            $y * CONFIG['spread-radius'],
            CONFIG['blur-radius'],
            CONFIG['spread-radius'],
            $color . (CONFIG['minify-css'] ? ',' : (($x + 1 < $width || $y + 1 < $height) ? ",\n" : ''))
        );
    }
}

file_put_contents('result.html', sprintf(file_get_contents('template.html'), $result));
echo 'Done! Result stored in file "result.html".';