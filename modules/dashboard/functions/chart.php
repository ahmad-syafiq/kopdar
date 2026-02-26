<?php  if (!defined('_VALID_BBC')) exit('No direct script access allowed');

function dashboard_chart_color(array $keys)
{
  // helper HSL → HEX (closure)
  $hsl_hex = function ($h, $s, $l) {
    $s /= 100;
    $l /= 100;

    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l - $c / 2;

    if ($h < 60)      [$r, $g, $b] = [$c, $x, 0];
    elseif ($h < 120) [$r, $g, $b] = [$x, $c, 0];
    elseif ($h < 180) [$r, $g, $b] = [0, $c, $x];
    elseif ($h < 240) [$r, $g, $b] = [0, $x, $c];
    elseif ($h < 300) [$r, $g, $b] = [$x, 0, $c];
    else              [$r, $g, $b] = [$c, 0, $x];

    return sprintf(
      '#%02x%02x%02x',
      (int)(($r + $m) * 255),
      (int)(($g + $m) * 255),
      (int)(($b + $m) * 255)
    );
  };

  $colors = [];
  $used   = [];

  $hue = rand(0, 360);
  $goldenAngle = 137.508;

  foreach ($keys as $key) {
    do {
      $hex = $hsl_hex($hue, 60, 65);
      $hue = fmod($hue + $goldenAngle, 360);
    } while (in_array($hex, $used, true));

    $colors[$key] = $hex;
    $used[] = $hex;
  }

  return $colors;
}
