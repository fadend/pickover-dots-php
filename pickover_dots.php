<?php

// Renders "million-point" structures as described in Clifford
// Pickover's book "Computers and the Imagination".

declare(strict_types=1);

function get_param(string $name, $default): string|int|float|bool
{
    return isset($_GET[$name]) ? $_GET[$name] : $default;
}

function get_int_param(string $name, int $default, int $min, int $max): int
{
    $value = (int)get_param($name, $default);
    return max($min, min($max, $value));
}

$a = (float) get_param('a', 2.0);
$b = (float) get_param('b', 1.5);
$width = get_int_param('width', 400, min: 1, max: 1000);
$height = get_int_param('height', 400, min: 1, max: 1000);
$numberOfPoints = get_int_param('num_points', 10000, min: 1, max: 1000000);
$showImage = (bool) get_param('image', false);

if ($showImage) {
    header('Content-type: image/png');

    $image = imageCreate($width, $height);

    // The first call to imageColorAllocate sets the image
    // background color. Set it to white (255, 255, 255).
    imageColorAllocate($image, 255, 255, 255);
    $black = imageColorAllocate($image, 0, 0, 0);
    $x = 0.5;
    $y = 0.5;
    $height3rd = $height / 3;
    $width3rd = $width / 3;
    for ($i = 0; $i < $numberOfPoints; $i++) {
        // The range of values for sin(r) + sin(s)**2
        // is [-1, 2]. To scale this range of values into the
        // the interval [0, 1], we multiply by 1/3 and add 1/3.
        // Finally, scaling by the display's width and height
        // yields the code below.
        imagesetpixel(
            $image,
            intval($width3rd * $x + $width3rd),
            intval($height3rd * $y + $height3rd),
            $black
        );
        $oldX = $x;
        $sinbx = sin($b * $x);
        $x = sin($b * $y) + $sinbx * $sinbx;
        $sinay = sin($a * $y);
        $y = sin($a * $oldX) + $sinay * $sinay;
    }
    imagePNG($image);
    imageDestroy($image);
} else { ?>
<html>
<head>
<title>Pickover dot structure: a=<?= $a ?>, b=<?= $b ?></title>
</head>
<body>
<h1>Pickover dot structure</h1>
<form method="GET">
a: <input size="5" name="a" value="<?= $a ?>">
<br>
b: <input size="5" name="b" value="<?= $b ?>">
<br>
width: <input size="4" name="width" value="<?= $width ?>">
<br>
height: <input size="4" name="height" value="<?= $height ?>">
<br>
number of points: <input size="7" name="num_points" value="<?= $numberOfPoints ?>">
<br>
<input type="submit">
</form>
<hr>
<img src="<?= "pickover_dots.php?width=$width&height=$height&a=$a&b=$b&num_points=$numberOfPoints&image=true" ?>"
  width="<?= $width ?>" height="<?= $height ?>" alt="Pickover dot structure">
</body>
</html>
<?php } ?>
