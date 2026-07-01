<?php
// Génère l'icône PNG pour apple-touch-icon (iOS)
$size = isset($_GET['size']) ? min(max((int)$_GET['size'], 16), 512) : 180;

$im = imagecreatetruecolor($size, $size);

// Palette cv (aubergine + gold)
$bg   = imagecolorallocate($im, 109, 21,  93);   // #6D155D
$text = imagecolorallocate($im, 211, 166, 37);   // #D3A625

// Fond arrondi : rectangle plein + coins découpés
imagefilledrectangle($im, 0, 0, $size - 1, $size - 1, $bg);
$r = (int)($size * 0.19); // rayon des coins (~96/512 * size)
// Peindre les 4 coins en transparent
$trans = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagecolortransparent($im, $trans);
// Coin haut-gauche
imagefilledrectangle($im, 0, 0, $r, $r, $trans);
imagefilledellipse($im, $r, $r, $r * 2, $r * 2, $bg);
// Coin haut-droit
imagefilledrectangle($im, $size - $r, 0, $size - 1, $r, $trans);
imagefilledellipse($im, $size - $r, $r, $r * 2, $r * 2, $bg);
// Coin bas-gauche
imagefilledrectangle($im, 0, $size - $r, $r, $size - 1, $trans);
imagefilledellipse($im, $r, $size - $r, $r * 2, $r * 2, $bg);
// Coin bas-droit
imagefilledrectangle($im, $size - $r, $size - $r, $size - 1, $size - 1, $trans);
imagefilledellipse($im, $size - $r, $size - $r, $r * 2, $r * 2, $bg);

// Texte "CV" avec police TTF si disponible
$rendered = false;
$fonts = [
    '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf',
    '/usr/share/fonts/truetype/freefont/FreeSerifBold.ttf',
    '/usr/share/fonts/truetype/liberation/LiberationSerif-Bold.ttf',
    '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
    '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
];
$fontSize = $size * 0.42;
foreach ($fonts as $font) {
    if (file_exists($font)) {
        $bbox = imagettfbbox($fontSize, 0, $font, 'CV');
        $tw   = $bbox[2] - $bbox[0];
        $th   = $bbox[1] - $bbox[7];
        $x    = (int)(($size - $tw) / 2) - $bbox[0];
        $y    = (int)(($size + $th) / 2) - $bbox[1];
        imagettftext($im, $fontSize, 0, $x, $y, $text, $font, 'CV');
        $rendered = true;
        break;
    }
}
if (!$rendered) {
    // Fallback : police intégrée GD (taille limitée)
    $f  = 5;
    $cw = imagefontwidth($f);
    $ch = imagefontheight($f);
    imagestring($im, $f, (int)(($size - $cw * 2) / 2), (int)(($size - $ch) / 2), 'CV', $text);
}

header('Content-Type: image/png');
header('Cache-Control: public, max-age=604800');
imagesavealpha($im, true);
imagepng($im);
imagedestroy($im);
